<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
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
                "first_name" => $this->first_name,
                "last_name" => $this->last_name,
                "email" => $this->email,
                "phone" => $this->phone,
                "avatar" => $this->avatar,
                "created_at" => $this->created_at?->toDateTimeString(),
            ],
            "relationships" => [
                'permissions' => $request->user()->getAllPermissions()->map(fn($permission) => $permission->name),
                'roles' => $request->user()->getRoleNames(),
            ],
            "meta" => [
                "is_active" => $this->status === 'active' ? true : false,
                "is_verified" => $this->email_verified_at !== null ? true : false,
            ]
        ];
    }
}
