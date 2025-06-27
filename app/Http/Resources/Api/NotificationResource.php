<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'type' => $this->data['type'] ?? 'notification',

            'attributes' => [
                'title' => $this->data['title'] ?? null,
                'message' => $this->data['message'] ?? null,
                'icon' => $this->data['icon'] ?? null,
                'link' => $this->data['link'] ?? null,
                'read_at' => $this->read_at,
                'created_at' => $this->created_at->toDateTimeString(),
            ],

            'meta' => [
                'read' => (bool) $this->read_at,
                'is_new' => $this->read_at === null,
            ]
        ];
    }
}
