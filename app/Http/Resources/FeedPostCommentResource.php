<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedPostCommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'postId' => (string) $this->feed_post_id,
            'authorName' => $this->author_name ?? $this->user?->name,
            'authorImage' => $this->author_avatar,
            'text' => $this->body,
            'createdAt' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
