<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Get ajax request filters parameters
                $filters = [
                    'created_by' => $request->get('employee'),
                    'branch_id' => $request->get('branch'),
                ];

                // Fetch by catalogue
                $catalogueFilter = $request->get('catalogue');

                // Build the query
                $salesQuery = Sale::query();

                // Apply filters dynamically using 'when' to avoid explicit checks
                foreach ($filters as $key => $value) {
                    $salesQuery->when(!empty($value), function ($query) use ($key, $value) {
                        $query->where($key, $value);
                    });
                }

                // Filter by catalogue if provided
                if (!empty($catalogueFilter)) {
                    $salesQuery->whereHas('saleItems', function ($query) use ($catalogueFilter) {
                        $query->where('catalogue_id', $catalogueFilter);
                    });
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

                // Helper for progress calculation
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
            }

            return view('portal.analytics.index');
        } catch (\Exception $exception) {
            Log::error(sprintf(
                'Error in %s: %s (File: %s, Line: %d)',
                __METHOD__,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
