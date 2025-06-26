<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomersResource extends JsonResource
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
            "attributes" => [
                "name" => $this->name,
                "email" => $this->email,
                "phone" => $this->phone,
                "address" => $this->address,
                "type" => $this->type,
                "category" => $this->category,
                "contact_person" => $this->contact_person,
                "tax_id" => $this->tax_id,
                "payment_terms" => $this->payment_terms,
                "credit_limit" => $this->credit_limit,
                "current_balance" => $this->current_balance,
                "latitude" => $this->latitude,
                "longitude" => $this->longitude,
            ]
        ];
    }
}
