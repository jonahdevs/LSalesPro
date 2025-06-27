<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehousesResource extends JsonResource
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
                "code" => $this->code,
                "name" => $this->name,
                "type" => $this->type,
                "address" => $this->address,
                "manager_email" => $this->manager_email,
                "phone" => $this->phone,
                "capacity" => $this->capacity,
                "latitude" => $this->latitude,
                "longitude" => $this->longitude,
            ]
        ];
    }
}
