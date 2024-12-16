<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Handle AJAX requests
            if ($request->ajax()) {
                // Fetch sales data grouped by day for the current week
                $salesData = DB::table('sales')
                    ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_revenue')
                    ->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                // Transform data for Chart.js
                $labels = $salesData->pluck('date')->toArray();
                $data = $salesData->pluck('total_revenue')->toArray();

                return response()->json([
                    'labels' => $labels,
                    'data' => $data,
                ]);
            }

            // Fetch statistics for the dashboard cards
            $salesCount = Sale::count();
            $totalRevenue = Sale::sum('total_amount');
            $totalCost = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                ->selectRaw('SUM(sale_items.quantity * products.buying_price) as total_cost')
                ->value('total_cost');
            $totalProfit = $totalRevenue - $totalCost;

            // Calculate progress dynamically (example logic)
            $progress = function ($value, $total) {
                return $total > 0 ? round(($value / $total) * 100) : 0;
            };

            $cards = [
                'number of sales' => [
                    'icon' => 'bi-cart',
                    'bg' => 'warning',
                    'value' => number_format($salesCount),
                    'progress' => $progress($salesCount, max($salesCount, 100)) // Example progress logic
                ],
                'revenue' => [
                    'icon' => 'bi-cash-stack',
                    'bg' => 'info',
                    'value' => 'Ksh ' . number_format($totalRevenue, 2),
                    'progress' => $progress($totalRevenue, $totalRevenue + $totalCost)
                ],
                'cost' => [
                    'icon' => 'bi-credit-card',
                    'bg' => 'danger',
                    'value' => 'Ksh ' . number_format($totalCost, 2),
                    'progress' => $progress($totalCost, $totalRevenue)
                ],
                'profit' => [
                    'icon' => 'bi-graph-up-arrow',
                    'bg' => 'success',
                    'value' => 'Ksh ' . number_format($totalProfit, 2),
                    'progress' => $progress($totalProfit, $totalRevenue)
                ],
            ];

            return view('portal.analytics.index', compact('cards'));
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ': ', [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'An error occurred while processing your request.'], 500);
            }

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}