<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => (string) $this->id,
            'type' => 'stock_transfer',
            'attributes' => [
                'quantity' => $this->quantity,
                'reference' => $this->reference,
                'created_at' => $this->created_at->toDateTimeString(),
            ],
            'relationships' => [
                'product' => [
                    "id" => (string) $this->product_id,
                    'name' => $this->product->name ?? null,
                    'sku' => $this->product->sku ?? null,
                ],
                'from_warehouse' => [
                    "id" => (string) $this->fromWarehouse->id ?? null,
                    "name" => $this->fromWarehouse->name ?? null,
                    "code" => $this->fromWarehouse->code ?? null,
                ],
                'to_warehouse' => [
                    "id" => (string) $this->toWarehouse->id ?? null,
                    "name" => $this->toWarehouse->name ?? null,
                    "code" => $this->toWarehouse->code ?? null,
                ],
            ]
        ];
    }
}
