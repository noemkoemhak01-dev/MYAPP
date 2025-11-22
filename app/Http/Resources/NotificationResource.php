<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'ctaLabel' => $this->cta_label,
            'ctaUrl' => $this->cta_url,
            'data' => $this->data,
            'isRead' => (bool) $this->read_at,
            'timestamp' => optional($this->created_at)->toIso8601String(),
            'read_at' => optional($this->read_at)->toIso8601String(),
        ];
    }
}
