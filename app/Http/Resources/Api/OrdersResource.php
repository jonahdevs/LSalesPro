<?php

namespace App\Http\Resources\Api;

use App\Helpers\LeyscoHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
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
            'attributes' => [
                'order_number' => $this->order_number,
                'status' => $this->status,
                'subtotal' => LeyscoHelpers::formatCurrency($this->subtotal),
                'discount' => LeyscoHelpers::formatCurrency($this->discount),
                'tax' => LeyscoHelpers::formatCurrency($this->tax),
                'total' => LeyscoHelpers::formatCurrency($this->total),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'relationships' => [
                'customer' => $this->whenLoaded('customer', fn() => [
                    'id' => (string) $this->customer->id,
                    'name' => $this->customer->name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ]),
                'items' => $this->whenLoaded('items', fn() => OrderItemsResource::collection($this->items)),
            ]
        ];
    }
}
