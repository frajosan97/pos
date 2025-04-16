<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment; // Adjust based on your model's namespace
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Get ajax request filters parameters
                $filters = [
                    'branch_id' => $request->get('branch'),
                    'payment_method_id' => $request->get('payment_method'),
                ];

                // Build the query
                $paymentsQuery = Payment::query();

                // Apply filters dynamically using 'when' to avoid explicit checks
                foreach ($filters as $key => $value) {
                    $paymentsQuery->when(!empty($value), function ($query) use ($key, $value) {
                        $query->where($key, $value);
                    });
                }

                // Fetch the payments and map the data
                $payments = $paymentsQuery->with(['sale.branch','paymentMethod'])
                    ->latest()
                    ->get()
                    ->map(function ($payment) {
                        return [
                            'branch' => $payment->sale?->branch->name ?? 'Unknown Branch',
                            'sale' => str_pad($payment->sale->id, 6, '0', STR_PAD_LEFT),
                            'amount' => number_format($payment->amount, 2),
                            'payment_method' => $payment->paymentMethod->name ?? 'Unknown Method',
                            'status'=> $payment->status,
                            'payment_date' => $payment->created_at->format('Y-m-d H:i:s'),
                            'action' => view('portal.payment.partials.actions', compact('payment'))->render(),
                        ];
                });

                // Return the payments data to DataTables
                return DataTables::of($payments)
                    ->rawColumns(['amount', 'status', 'action'])
                    ->make(true);
            } else {
                return view('portal.payment.index'); // Return the index view if not an AJAX request
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500); // Generic error message
        }
    }
}
