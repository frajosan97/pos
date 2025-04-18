<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\Payment;
use App\Models\Products;
use App\Models\Commission;
use App\Models\Company;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;
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
                    'user_id' => $request->get('employee'),
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
                $sales = $salesQuery->with('user')
                    ->latest()
                    ->get()
                    ->map(function ($sale) {
                        // Bill calculations
                        $total_billed = $sale->total_amount;
                        $total_paid = $sale->payments->sum('amount');
                        $total_balance = $total_billed - $total_paid;

                        // Status button
                        $statusBtn = ($sale->status == 'paid')
                            ? '<strong class="text-success text-capitalize"><i class="fas fa-check-circle"></i> ' . $sale->status . '</strong>'
                            : '<strong class="text-danger text-capitalize"><i class="fas fa-times-circle"></i> ' . $sale->status . '</strong>';

                        return [
                            'invoice_number' => invoiceNumber($sale->id),
                            'total_billed' => '<div class="text-end">' . number_format($total_billed, 2) . '</div>',
                            'total_paid' => '<div class="text-end">' . number_format($total_paid, 2) . '</div>',
                            'total_balance' => '<div class="text-end">' . number_format($total_balance, 2) . '</div>',
                            'pay_method' => ucwords($sale->payments->first()->paymentMethod->name) ?? 'Unknown Method',
                            'cashier' => $sale->user->name ?? 'Unknown Cashier',
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
                'data.total_price' => 'required|numeric|min:0',
                'data.total_paid' => 'required|numeric|min:0',
                'data.cart' => 'required|array|min:1',
                'data.cart.*.quantity' => 'required|integer|min:1',
                'data.cart.*.product.id' => 'required|exists:products,id',
                'data.cart.*.product.quantity' => 'required|integer|min:1',
                'data.cart.*.product.normal_price' => 'required|numeric|min:0',
                'data.cart.*.product.whole_sale_price' => 'required|numeric|min:0',
                'data.cart.*.product.agent_price' => 'required|numeric|min:0',
                'data.payments' => 'required|array|min:1',
                'data.payments.*.id' => 'required|integer',
                'data.payments.*.name' => 'required|string|in:cash,mpesa',
                'data.payments.*.amount' => 'required|numeric|min:0',
                'data.payments.*.reference' => 'nullable|string',
            ]);

            // Transaction to ensure data integrity
            DB::beginTransaction();

            // prepare reference id
            $created_by = Auth::user()->id;

            // Store the sale
            $sale = Sale::create([
                'branch_id' => $validated['branch_id'],
                'user_id' => $created_by,
                'customer_id' => $validated['customer_id'],
                'sale_type' => $validated['sale_type'],
                'total_amount' => $validated['data']['total_price'],
                'status' => $validated['data']['total_paid'] >= $validated['data']['total_price'] ? 'paid' : 'pending',
                'created_by' => $created_by,
            ]);

            // Store each sale item
            foreach ($validated['data']['cart'] as $item) {
                $product = Products::find($item['product']['id']);
                if ($product) {
                    // Create sale item record
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'catalogue_id' => $product->catalogue_id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['product'][$validated['sale_type']],
                        'total' => $item['quantity'] * $item['product'][$validated['sale_type']],
                    ]);

                    // Update product stock and sold quantity
                    $product->decrement('quantity', $item['quantity']);
                    $product->increment('sold_quantity', $item['quantity']);

                    $commission_by = Company::first()->commission_by;

                    if (!$commission_by === 'gross_sale') {
                        switch ($commission_by) {
                            case 'product':
                                $commissionEarned = $item['product'][$validated['sale_type']] * $item['product']['commission_on_sale'];
                                break;
                            default:
                                $commissionEarned = ($item['product'][$validated['sale_type']] - $product->buying_price) * Auth::user()->commission_rate;
                                break;
                        }

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

            if ($commission_by === 'gross_sale') {
                $commissionEarned = $validated['data']['total_price'] - $validated['data']['total_paid'] * Auth::user()->commission_rate;
                Commission::create([
                    'user_id' => $created_by,
                    'product_id' => $product->id,
                    'unit_commission' => $commissionEarned,
                    'quantity_sold' => '1',
                    'commission_amount' => $commissionEarned,
                ]);
            }

            // Process payments
            foreach ($validated['data']['payments'] as $payment) {

                // Check if payment is mpesa and update the status
                $reference = $payment['reference'] === 'NULL' ? null : $payment['reference'];

                // Create payment record
                $currentPayment = Payment::create([
                    'branch_id' => $validated['branch_id'],
                    'sale_id' => $sale->id,
                    'amount' => $payment['amount'],
                    'payment_method_id' => $payment['id'],
                    'status' => $payment['amount'] >= $validated['data']['total_price'] ? 'completed' : 'pending',
                    'payment_date' => now(),
                    'reference_id' => $reference,
                ]);

                if ($currentPayment && $payment['name'] === 'mpesa') {
                    MpesaPayment::where('id', $reference)->update(['use_status' => 'used']);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['success' => 'Sale and payment recorded successfully', 'sale_id' => $sale->id], 201);
        } catch (\Exception $exception) {
            // Rollback in case of an error
            DB::rollBack();
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'Message: ' . $exception->getMessage()], 500); // Generic error message
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

            $generator = new BarcodeGeneratorPNG();
            $barcode = base64_encode($generator->getBarcode($id, $generator::TYPE_CODE_128));

            // Return the invoice view with the sale, total cost, and profit
            return view('portal.sale.show', compact(['sale', 'barcode']));
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
                $sales = $salesQuery
                    ->latest()
                    ->get()
                    ->map(function ($sale) {

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
                    ->rawColumns(['product', 'quantity', 'unit_price', 'total_price', 'action'])
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
