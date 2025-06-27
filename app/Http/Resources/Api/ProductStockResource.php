<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this;

        $warehouses = $this->whenLoaded('warehouses');

        $warehouseData = $warehouses->map(function ($warehouse) use ($product) {
            $stock = $warehouse->stock->first()?->quantity ?? 0;

            $reserved = $product->reservations()
                ->where('warehouse_id', $warehouse->id)
                ->where('expires_at', '>', now())
                ->where('status', 'reserved')
                ->sum('quantity');

            return [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'stock' => $stock,
                'reserved' => $reserved,
                'available' => max($stock - $reserved, 0),
            ];
        });

        return [
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'total_stock' => $warehouseData->sum('stock'),
            'total_reserved' => $warehouseData->sum('reserved'),
            'available_stock' => $warehouseData->sum('available'),
            'warehouses' => $warehouseData,
        ];
    }
}
