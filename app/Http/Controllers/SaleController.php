<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\Payment;
use App\Models\Products;
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
                $sales = Sale::with(['payments.paymentMethod']) // Eager load payments and their payment methods
                    ->get()
                    ->map(function ($sale) {
                        // Bill calculations
                        $total_billed = $sale->total_amount;
                        $total_paid = $sale->payments->sum('amount');
                        $total_balance = $total_billed - $total_paid;
                        // Status btn
                        $statusBtn = ($sale->status == 'paid')
                            ? '<strong class="text-success text-capitalize"><i class="fas fa-check-circle"></i> ' . $sale->status . '</strong>'
                            : '<strong class="text-danger text-capitalize"><i class="fas fa-times-circle"></i> ' . $sale->status . '</strong>';

                        return [
                            'invoice_number' => str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                            'total_billed' => '<div class="text-end">' . number_format($total_billed, 2) . '</div>',
                            'total_paid' => '<div class="text-end">' . number_format($total_paid, 2) . '</div>',
                            'total_balance' => '<div class="text-end">' . number_format($total_balance, 2) . '</div>',
                            'pay_method' => ucwords($sale->payments->first()->paymentMethod->name) ?? 'Unknown Method', // Access the payment method name
                            'cashier' => User::where('id', $sale->created_by)->value('name') ?? 'Unknown Cashier',
                            'status' => $statusBtn,
                            'action' => view('portal.sale.partials.sale_actions', compact('sale'))->render(),
                            'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
                        ];
                    });

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
                'sale_type' => 'required|string',
                'data.total' => 'required|numeric|min:0',
                'data.paid' => 'required|numeric|min:0',
                'data.payment_reference_id' => 'string',
                'data.payment_method' => 'required|string',
                'data.payment_method_name' => 'required|string',
                'data.cart' => 'required|array|min:1',
                'data.cart.product.*.product_id' => 'required|exists:products,id',
                'data.cart.product.*.quantity' => 'required|integer|min:1',
                'data.cart.product.*.price' => 'required|numeric|min:0',
            ]);

            // Transaction to ensure data integrity
            DB::beginTransaction();

            $customer_id = ($request->input('data.customer_id'))
                ? $request->input('data.customer_id') : null;
            $reference_id = (!($validated['data']['payment_reference_id'] == '0'))
                ? $validated['data']['payment_reference_id'] : null;
            $created_by = (Auth::user()->id)
                ? Auth::user()->id : null;

            // Store the sale
            $sale = Sale::create([
                'customer_id' => $customer_id,
                'sale_type' => $validated['sale_type'],
                'total_amount' => $validated['data']['total'],
                'status' => $validated['data']['paid'] >= $validated['data']['total'] ? 'paid' : 'pending',
                'created_by' => $created_by,
            ]);

            // Store each sale item
            foreach ($validated['data']['cart'] as $item) {
                // Create the sale item record
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product'][$validated['sale_type']],
                    'total' => $item['quantity'] * $item['product'][$validated['sale_type']],
                ]);

                // Update the product sold quantity
                $product = Products::find($item['product']['id']);
                if ($product) {
                    $product->sold_quantity += $item['quantity']; // Increment the sold quantity
                    $product->quantity -= $item['quantity']; // Decrease the available quantity
                    $product->save(); // Save the updated product
                }
            }

            // Record the payment
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'amount' => $validated['data']['paid'],
                'payment_method' => $validated['data']['payment_method'],
                'status' => $validated['data']['paid'] >= $validated['data']['total'] ? 'completed' : 'pending',
                'payment_date' => now(),
                'reference_id' => $reference_id,
            ]);

            if ($payment) {
                if ($request->input('data.payment_method_name') == 'mpesa') {
                    // Find the mpesa_response_update by ID or fail if not found
                    $mpesa_response_update = MpesaPayment::findOrFail($reference_id);
                    // Update the mpesa_response_update name
                    $mpesa_response_update->update([
                        'use_status' => 'used',
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => 'Sale and payment recorded successfully'], 201);
        } catch (\Exception $exception) {
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
}
