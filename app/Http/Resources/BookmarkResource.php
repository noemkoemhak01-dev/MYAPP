<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'source' => $this->source,
            'post' => new FeedPostResource($this->whenLoaded('post')),
        ];
    }
}
