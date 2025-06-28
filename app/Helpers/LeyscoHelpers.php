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
}
