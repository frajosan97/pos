<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\Payment;
use App\Models\Products;
use App\Models\Commission;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Get ajax request filters parameters
                $filters = [
                    'created_by' => $request->get('employee'),
                    'branch_id' => $request->get('branch'),
                ];

                // Build the query
                $salesQuery = Sale::query();

                // Apply filters dynamically using 'when' to avoid explicit checks
                foreach ($filters as $key => $value) {
                    $salesQuery->when(!empty($value), function ($query) use ($key, $value) {
                        $query->where($key, $value);
                    });
                }

                // Fetch the sales and map the data
                $sales = $salesQuery->get()->map(function ($sale) {
                    // Bill calculations
                    $total_billed = $sale->total_amount;
                    $total_paid = $sale->payments->sum('amount');
                    $total_balance = $total_billed - $total_paid;

                    // Status button
                    $statusBtn = ($sale->status == 'paid')
                        ? '<strong class="text-success text-capitalize"><i class="fas fa-check-circle"></i> ' . $sale->status . '</strong>'
                        : '<strong class="text-danger text-capitalize"><i class="fas fa-times-circle"></i> ' . $sale->status . '</strong>';

                    return [
                        'invoice_number' => str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                        'total_billed' => '<div class="text-end">' . number_format($total_billed, 2) . '</div>',
                        'total_paid' => '<div class="text-end">' . number_format($total_paid, 2) . '</div>',
                        'total_balance' => '<div class="text-end">' . number_format($total_balance, 2) . '</div>',
                        'pay_method' => ucwords($sale->payments->first()->paymentMethod->name) ?? 'Unknown Method',
                        'cashier' => User::where('id', $sale->created_by)->value('name') ?? 'Unknown Cashier',
                        'status' => $statusBtn,
                        'action' => view('portal.sale.partials.sale_actions', compact('sale'))->render(),
                        'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
                    ];
                });

                // Return the sales data to DataTables
                return DataTables::of($sales)
                    ->rawColumns(['total_billed', 'total_paid', 'total_balance', 'status', 'action'])
                    ->make(true);
            } else {
                return view('portal.sale.index'); // Return the index view if not an AJAX request
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500); // Generic error message
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('portal.sale.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'customer_id' => 'nullable|string',
                'sale_type' => 'required|string',
                'branch_id' => 'required|string',
                'data.totalAmount' => 'required|numeric|min:0',
                'data.cart' => 'required|array|min:1',
                'data.cart.product.*.product_id' => 'required|exists:products,id',
                'data.cart.product.*.quantity' => 'required|integer|min:1',
                'data.cart.product.*.price' => 'required|numeric|min:0',
                'data.payments' => 'required|array|min:1',
                'data.payments.*.payment_method_name' => 'required|string|in:cash,mpesa',
                'data.payments.*.amount' => 'required|numeric|min:0',
                'data.payments.*.payment_method_id' => 'required|integer',
            ]);

            // Transaction to ensure data integrity
            DB::beginTransaction();

            $reference_id = (!($validated['data']['payment_reference_id'] == '0'))
                ? $validated['data']['payment_reference_id'] : null;
            $created_by = (Auth::user()->id)
                ? Auth::user()->id : null;

            // Store the sale
            $sale = Sale::create([
                'branch_id' => $validated['branch_id'],
                'customer_id' => $validated['customer_id'],
                'sale_type' => $validated['sale_type'],
                'total_amount' => $validated['data']['totalAmount'],
                'status' => $validated['data']['paid'] >= $validated['data']['totalAmount'] ? 'paid' : 'pending',
                'created_by' => $created_by,
            ]);

            // Store each sale item
            foreach ($validated['data']['cart'] as $item) {
                // Create the sale item record
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'catalogue_id' => $item['product']['catalogue_id'],
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product'][$validated['sale_type']],
                    'total' => $item['quantity'] * $item['product'][$validated['sale_type']],
                ]);

                if ($saleItem) {
                    // Update the product sold quantity
                    $product = Products::find($item['product']['id']);
                    if ($product) {
                        $product->sold_quantity += $item['quantity']; // Increment the sold quantity
                        $product->quantity -= $item['quantity']; // Decrease the available quantity
                        $product->save(); // Save the updated product

                        // Create the commission record if applicable (Selling price - buying price * commission rate)
                        $commissionEarned = ($item['product'][$validated['sale_type']] - $product->buying_price) * Auth::user()->commission_rate;
                        Commission::create([
                            'user_id' => $created_by,
                            'product_id' => $product->id,
                            'unit_commission' => $commissionEarned,
                            'quantity_sold' => $item['quantity'],
                            'commission_amount' => $commissionEarned * $item['quantity'],
                        ]);
                    }
                }
            }

            // Process each payment method (Cash, Mpesa, etc.)
            foreach ($validated['data']['payments'] as $payment) {
                // Record the payment
                Payment::create([
                    'branch_id' => $validated['branch_id'],
                    'sale_id' => $sale->id,
                    'amount' => $payment['amount'],
                    'payment_method' => $payment['payment_method_name'],
                    'status' => $payment['amount'] >= $validated['data']['totalAmount'] ? 'completed' : 'pending',
                    'payment_date' => now(),
                    'reference_id' => $reference_id,
                ]);

                // If payment method is Mpesa, update the Mpesa record (if applicable)
                if ($payment['payment_method_name'] == 'mpesa') {
                    $mpesa_response_update = MpesaPayment::findOrFail($reference_id);
                    $mpesa_response_update->update([
                        'use_status' => 'used',
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['success' => 'Sale and payment recorded successfully', 'sale_id' => $sale->id], 201);
        } catch (\Exception $exception) {
            // Rollback in case of an error
            DB::rollBack();
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }


    /**
     * Show the form for showing a resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            // Fetch the sale by its ID, along with related sale items and customer
            $sale = Sale::with(['saleItems.product', 'payments.paymentMethod', 'customer'])->findOrFail($id);

            // Return the invoice view with the sale, total cost, and profit
            return view('portal.sale.show', compact('sale'));
        } catch (\Exception $exception) {
            Log::error('Error viewing invoice: ' . $exception->getMessage());
            return redirect()->route('sale.index')->with('error', 'Invoice not found');
        }
    }

    public function catalogue(Request $request)
    {
        try {
            return view('portal.sale.catalogue_sale');
        } catch (\Exception $exception) {
            Log::error('Error viewing invoice: ' . $exception->getMessage());
            return redirect()->route('sale.index')->with('error', 'Catalogue not found');
        }
    }

    public function product(Request $request)
    {
        try {
            return view('portal.sale.product_sale');
        } catch (\Exception $exception) {
            Log::error('Error viewing invoice: ' . $exception->getMessage());
            return redirect()->route('sale.index')->with('error', 'Product not found');
        }
    }

    public function catProFetch(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Get ajax request filters parameters
                $filters = [
                    'category_id' => $request->get('category'),
                    'product_id' => $request->get('product'),
                ];

                // Build the query
                $salesQuery = SaleItem::with('product');

                // Apply filters dynamically using 'when' to avoid explicit checks
                foreach ($filters as $key => $value) {
                    $salesQuery->when(!empty($value), function ($query) use ($key, $value) {
                        $query->where($key, $value);
                    });
                }

                // Fetch the sales and map the data
                $sales = $salesQuery->get()->map(function ($sale) {

                    return [
                        'product' => ucwords($sale->product->name),
                        'quantity' => '<div class="text-end">' . number_format($sale->quantity, 2) . '</div>',
                        'unit_price' => '<div class="text-end">' . number_format($sale->price, 2) . '</div>',
                        'total_price' => '<div class="text-end">' . number_format($sale->total, 2) . '</div>',
                        'action' => ''
                    ];
                });

                // Return the sales data to DataTables
                return DataTables::of($sales)
                    ->rawColumns(['total_billed', 'total_paid', 'total_balance', 'status', 'action'])
                    ->make(true);
            } else {
                return view('portal.sale.index'); // Return the index view if not an AJAX request
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500); // Generic error message
        }
    }
}
