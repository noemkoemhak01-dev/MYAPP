<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FeedPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'topic' => $this->topic,
            'category' => $this->category,
            'type' => $this->type,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'coverImage' => $this->cover_image,
            'coverImageUrl' => $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null,
            'videoPath' => $this->video_url,
            'createdAt' => optional($this->published_at ?? $this->created_at)->toIso8601String(),
            'authorId' => (string) $this->user_id,
            'authorName' => $this->author?->name,
            'authorImage' => $this->author?->avatar ? Storage::disk('public')->url($this->author->avatar) : null,
            'likeCount' => (int) $this->like_count,
            'commentCount' => (int) $this->comment_count,
            'shareCount' => (int) $this->share_count,
            'isLiked' => (bool) ($this->pivot?->is_liked ?? false),
            'comments' => FeedPostCommentResource::collection($this->whenLoaded('comments')),
            'media' => FeedPostMediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
