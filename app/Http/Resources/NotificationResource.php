<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'isRead' => $this->is_read,
            'timestamp' => $this->created_at->toIso8601String(),
            'createdAt' => $this->created_at->toIso8601String(),
        ];
    }
}