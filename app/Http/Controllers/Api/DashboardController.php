<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LeyscoHelpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    //
    public function summary(Request $request)
    {
        $range = $request->query('range', 'month');

        $cacheKey = "dashboard:summary:$range";


        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($range) {
            [$start, $end] = LeyscoHelpers::range($range);

            $orders = Order::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled');

            $totalSales = $orders->sum('total');
            $orderCount = $orders->count();
            $aov = $orderCount > 0 ? round($totalSales / $orderCount, 2) : 0;

            $turnoverRate = LeyscoHelpers::calculateTurnoverRate($start, $end);

            return response()->json([
                'total_sales' => $totalSales,
                'order_count' => $orderCount,
                'average_order_value' => $aov,
                'inventory_turnover_rate' => $turnoverRate,
            ]);
        });
    }

    public function salesPerformance(Request $request)
    {
        [$start, $end] = LeyscoHelpers::range($request->query('range', 'month'));

        $sales = Order::selectRaw("DATE(created_at) as date, SUM(total) as total")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $sales->pluck('date'),
            'data' => $sales->pluck('total')
        ]);
    }
}
