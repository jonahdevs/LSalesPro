<?php

namespace App\Services;

use App\Helpers\LeyscoHelpers;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockReservation;
use App\Models\Warehouse;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    use HttpResponses;

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($data['customer_id']);
            $totals = $this->calculateTotals($data);

            // Validate credit limit
            $this->validateCreditLimit($customer, $totals['total']);

            // check stock
            $this->checkAndReserveStock($data['items']);

            // generate order number
            $orderNumber = LeyscoHelpers::generateOrderNumber();

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => $orderNumber,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'status' => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineTotal = $product->price * $item['quantity'];

                $lineDiscount = 0;

                $lineDiscount = 0;
                if (isset($item['discount'])) {
                    $lineDiscount = $this->applyDiscount($lineTotal, $item['discount']);
                }

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'discount' => $lineDiscount,
                ]);
            }

            DB::commit();
            return $order;
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->error(null, $th->getMessage(), 500);

        }
    }


    public function calculateTotals(array $data): array
    {
        $subtotal = $discount = $tax = 0;

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if (!$product) {
                return $this->error(null, 'Unprocessable content', 404);
            }

            $lineTotal = $product->price * $item['quantity'];

            // handle line level discount
            if (isset($item['discount'])) {
                $discount += $this->applyDiscount($lineTotal, $item['discount']);
            }

            $subtotal += $lineTotal;
        }

        // order level discount
        if (isset($data['order_discount'])) {
            $discount += $this->applyDiscount($subtotal, $data['order_discount']);
        }

        $tax = LeyscoHelpers::calculateTax($subtotal - $discount);
        $total = $subtotal - $discount + $tax;

        return compact('subtotal', 'discount', 'tax', 'total');
    }

    public function applyDiscount(float $amount, array $discount): float
    {
        return match ($discount['type']) {
            'percentage' => ($discount['value'] / 100) * $amount,
            'fixed' => min($discount['value'], $amount),
            default => 0
        };
    }


    protected function validateCreditLimit(Customer $customer, float $total)
    {
        if ($customer->available_credit < $total) {
            throw ValidationException::withMessages([
                'credit' => 'Customer credit limit exceeded'
            ]);
        }
    }

    protected function checkAndReserveStock(array $items)
    {
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            $stock = Stock::with([
                'reservations' => function ($query) {
                    $query->where('status', 'reserved')
                        ->where('expires_at', '>', now());
                }
            ])->where('product_id', $productId)->get();


            // Find the first stock with enough available quantity
            $stockWithQuantity = $stock->first(function ($stock) use ($quantity) {
                $reserved = $stock->reservations->sum('quantity');
                $available = $stock->quantity - $reserved;
                return $available >= $quantity;
            });

            if (!$stockWithQuantity) {
                $product = Product::find($productId);
                $productName = $product?->name ?? 'Unknown Product';

                throw ValidationException::withMessages([
                    'stock' => "Insufficient stock for {$productName} across all warehouses."
                ]);
            }

            StockReservation::create([
                'stock_id' => $stockWithQuantity->id,
                'reserved_by' => auth()->id() ?? 1, // default for now
                'quantity' => $quantity,
                'expires_at' => now()->addMinutes(30),
                'status' => 'reserved',
            ]);
        }
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        $validStatus = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];


        if (!in_array($status, $validStatus)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid status'
            ]);
        }

        if ($order->status == 'shipped' && $status === 'cancelled') {
            throw ValidationException::withMessages([
                'status' => 'Cannot cancel a shipped order'
            ]);
        }

        $order->update(['status' => $status]);
    }



}
