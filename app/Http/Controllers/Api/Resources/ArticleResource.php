<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isBookmarked = false;
        if (auth('sanctum')->check()) {
            $isBookmarked = $this->isBookmarkedBy(auth('sanctum')->id());
        }

        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'category' => $this->category ? $this->category->name : null,
            'author' => $this->author ? $this->author->name : null,
            'authorImage' => $this->author && $this->author->avatar 
                ? url('storage/' . $this->author->avatar) 
                : '',
            'imageUrl' => $this->image_url ?? '',
            'publishedAt' => $this->published_at ? $this->published_at->toIso8601String() : null,
            'views' => $this->views,
            'readTime' => $this->read_time,
            'isBookmarked' => $isBookmarked,
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}