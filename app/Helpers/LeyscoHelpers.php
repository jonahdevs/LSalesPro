<?php

namespace App\Helpers;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeyscoHelpers
{
    public static function formatCurrency($amount, $currency = 'KES'): string
    {
        return $currency . ' ' . number_format($amount, 2) . ' /=';
    }

    public static function generateOrderNumber(): string
    {
        $datePart = date('Y-m');
        $randomPart = strtoupper(Str::random(3));

        return "ORD-{$datePart}-{$randomPart}";
    }

    public static function calculateTax($amount, $rate = 0.16): float
    {
        return round($amount * $rate, 2);
    }

    public static function range(string $range): array
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

    public static function calculateTurnoverRate(Carbon $start, Carbon $end): float
    {
        $cogs = OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'cancelled')
        )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(order_items.quantity * products.cost_price) as total_cogs')
            ->value('total_cogs') ?? 0;

        $endingInventory = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->selectRaw('SUM(stocks.quantity * products.cost_price) as total_inventory_value')
            ->value('total_inventory_value') ?? 0;

        return $endingInventory > 0 ? round($cogs / $endingInventory, 2) : 0.0;
    }
}
