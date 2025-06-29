<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowStockProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            "attributes" => [
                'sku' => $this->sku,
                'name' => $this->name,
                'available_quantity' => $this->available_quantity,
                'reorder_level' => $this->reorder_level,
            ]
        ];
    }
}
