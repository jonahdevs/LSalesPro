<?php

namespace App\Http\Resources\Api;

use App\Helpers\LeyscoHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
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
                'quantity' => $this->quantity,
                'discount' => LeyscoHelpers::formatCurrency($this->discount),
                'price' => LeyscoHelpers::formatCurrency(optional($this->product)->price),
                'total' => LeyscoHelpers::formatCurrency(optional($this->product) ? $this->product->price * $this->quantity : 0),
            ],
            "relationships" => [
                "product" => [
                    "id" => $this->product_id,
                    "name" => optional($this->product)->name,
                ]
            ]
        ];
    }
}
