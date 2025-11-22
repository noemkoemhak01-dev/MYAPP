<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedTopicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'category' => $this->category,
            'description' => $this->description,
            'isFeatured' => (bool) $this->is_featured,
            'trendScore' => (int) $this->trend_score,
        ];
    }
}
