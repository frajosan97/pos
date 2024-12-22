<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use App\Models\Ward;
use App\Models\Location;
use App\Models\MpesaPayment;
use App\Models\PaymentMethod;
use App\Models\Products;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function analytics(Request $request)
    {
        try {
            $fetchType = $request->get('fetchType');
            $fetchTypeValue = $request->get('fetchTypeValue');

            // Determine the fetch column and value
            $fetchFilters = match ($fetchType) {
                'branch' => ['column' => 'branch_id', 'value' => $fetchTypeValue ?? Auth::user()->branch?->id],
                'employee' => ['column' => 'created_by', 'value' => $fetchTypeValue ?? Auth::id()],
                default => ['column' => null, 'value' => null],
            };

            // Build the sales query
            $salesQuery = Sale::query();
            if ($fetchFilters['column'] && $fetchFilters['value']) {
                $salesQuery->where($fetchFilters['column'], $fetchFilters['value']);
            }

            // Get relevant sales IDs
            $salesIds = $salesQuery->pluck('id');

            // Dashboard statistics
            $salesCount = $salesQuery->count();
            $totalRevenue = $salesQuery->sum('total_amount');
            $totalCost = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                ->whereIn('sale_items.sale_id', $salesIds)
                ->sum(DB::raw('sale_items.quantity * products.buying_price'));
            $totalProfit = $totalRevenue - $totalCost;
            $progress = fn($value, $total) => $total > 0 ? round(($value / $total) * 100) : 0;

            // Daily sales data grouped by date for the current year
            $dailyData = $salesQuery
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_revenue')
                ->whereYear('created_at', now()->year)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Prepare labels and data for the chart
            $labels = $dailyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d, Y'))->toArray();
            $data = $dailyData->pluck('total_revenue')->toArray();

            // Helper function for card data
            $createCard = fn($icon, $bg, $value, $progress) => compact('icon', 'bg', 'value', 'progress');

            // Prepare dashboard card data
            $cards = [
                'number of sales' => $createCard('bi-cart', 'warning', number_format($salesCount), $progress($salesCount, max($salesCount, 100))),
                'revenue'        => $createCard('bi-cash-stack', 'info', 'Ksh ' . number_format($totalRevenue, 2), $progress($totalRevenue, $totalRevenue + $totalCost)),
                'cost'           => $createCard('bi-credit-card', 'danger', 'Ksh ' . number_format($totalCost, 2), $progress($totalCost, $totalRevenue)),
                'profit'         => $createCard('bi-graph-up-arrow', 'success', 'Ksh ' . number_format($totalProfit, 2), $progress($totalProfit, $totalRevenue)),
            ];

            // Response data
            $responseData = ['cards' => $cards, 'labels' => $labels, 'data' => $data];

            return response()->json($responseData, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ': ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function constituency(Request $request, string $county_id)
    {
        try {
            $data = Constituency::where('county_id', $county_id)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function ward(Request $request, string $constituency_id)
    {
        try {
            $data = Ward::where('constituency_id', $constituency_id)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function location(Request $request, string $ward_id)
    {
        try {
            $data = Location::where('ward_id', $ward_id)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function product(Request $request, string $barcode)
    {
        try {
            $data = Products::where('barcode', $barcode)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function paymentMethods(Request $request)
    {
        try {
            $data = PaymentMethod::all();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function mpesaPaymants(Request $request)
    {
        try {
            $data = MpesaPayment::whereNot('use_status', 'used')->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }
}
