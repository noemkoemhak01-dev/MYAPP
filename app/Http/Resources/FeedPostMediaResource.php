<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FeedPostMediaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => $this->media_type,
            'url' => Storage::disk('public')->url($this->path),
            'position' => $this->position,
            'meta' => $this->meta,
        ];
    }
}
