<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ProductsResource extends JsonResource
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
            "attributes" => [
                "name" => $this->name,
                "sku" => $this->sku,
                "description" => $this->description,
                "price" => Number::currency($this->price, 'kes'),
                "tax_rate" => $this->tax_rate,
                "unit" => $this->unit,
                "packaging" => $this->packaging,
                "min_order_quantity" => $this->min_order_quantity,
                "reorder_level" => $this->reorder_level,
            ],
            'relationships' => [
                'subcategory' => $this->whenLoaded('category', fn() => $this->category->name),
                'category' => $this->whenLoaded('category', fn() => $this->category->parent?->name)
            ]
        ];
    }
}
