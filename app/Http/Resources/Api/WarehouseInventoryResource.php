<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class WarehouseInventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "type" => "inventory",
            'attributes' => [
                "quantity" => $this->quantity,
                'warehouse_id' => $this->warehouse_id,
                'updated_at' => $this->updated_at?->toDateTimeString(),
            ],
            "relationships" => [
                'product' => [
                    "id" => (string) $this->product_id,
                    "name" => $this->product->name ?? null,
                    'sku' => $this->product->sku ?? null,
                    "price" => Number::currency($this->product->price ?? 0, 'kes'),
                ]
            ]
        ];
    }
}
