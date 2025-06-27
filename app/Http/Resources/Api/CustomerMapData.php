<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerMapData extends JsonResource
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
                "latitude" => $this->latitude,
                "longitude" => $this->longitude,
            ],
            "relationships" => [
                'territory' => $this->whenLoaded('territory', function () {
                    return [
                        'id' => (string) $this->territory->id,
                        'name' => $this->territory->name,
                    ];
                }),
            ],
        ];
    }
}
