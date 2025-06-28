<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LeyscoHelpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function summary(Request $request)
    {
        // Validate the range parameter
        $range = $request->query('range', 'month');

        //
        $cacheKey = "dashboard:summary:$range";

        // Cache the summary data for 5 minutes
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($range) {
            [$start, $end] = $this->range($range);

            $orders = Order::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled');

            $totalSales = $orders->sum('total');
            $orderCount = $orders->count();
            $averageOrderValue = $orderCount > 0 ? round($totalSales / $orderCount, 2) : 0;

            $turnoverRate = $this->calculateTurnoverRate($start, $end);

            return response()->json([
                'total_sales' => $totalSales,
                'order_count' => $orderCount,
                'average_order_value' => $averageOrderValue,
                'inventory_turnover_rate' => $turnoverRate,
            ]);
        });
    }

    public function salesPerfomance(Request $request)
    {
        // Validate the range parameter
        [$start, $end] = $this->range($request->query('range', 'month'));

        // Fetch sales data grouped by date
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

    public function inventoryStatus()
    {
        // Cache the inventory status for 10 minutes
        return Cache::remember('dashboard:inventory-status', now()->addMinutes(10), function () {

            // Fetch products with their categories, stock, and reservations
            return Product::with('category', 'stock', 'reservations')
                ->get()
                ->groupBy(fn($product) => $product->category->name ?? 'Uncategorized')
                ->map(function ($products) {
                    return [
                        'stock' => $products->sum(fn($product) => $product->available_quantity),
                        'products_count' => $products->count(),
                    ];
                })
                ->map(fn($data, $category) => [
                    'category' => $category,
                    'available_stock' => $data['stock'],
                    'products_count' => $data['products_count'],
                ])
                ->values(); // reset keys
        });
    }

    public function topProducts()
    {
        // Cache the top products for 10 minutes
        return Cache::remember('dashboard:top-products', now()->addMinutes(10), function () {

            // Fetch top 5 products by quantity sold
            return DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(order_items.quantity) as sold'))
                ->groupBy('products.name')
                ->orderByDesc('sold')
                ->limit(5)
                ->get();
        });
    }


    protected static function range(string $range): array
    {
        return match ($range) {
            'today' => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'quarter' => [Carbon::now()->firstOfQuarter(), Carbon::now()->lastOfQuarter()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }


    protected static function calculateTurnoverRate(Carbon $start, Carbon $end): float
    {
        $costOfGoodsSold = OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
        )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_items.quantity * products.price) as total_cogs')
            ->value('total_cogs') ?? 0;

        $endingInventory = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(stocks.quantity * products.price) as total_inventory_value')
            ->value('total_inventory_value') ?? 0;

        return $endingInventory > 0 ? round($costOfGoodsSold / $endingInventory, 2) : 0.0;
    }
}
