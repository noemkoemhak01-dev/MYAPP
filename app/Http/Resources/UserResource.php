<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar ? Storage::disk('public')->url($this->avatar) : null,
            'bio' => $this->bio,
            'is_admin' => $this->is_admin,
            'role' => $this->role,
            'stats' => [
                'postsCount' => (int) ($this->feed_posts_count ?? $this->feedPosts()->count()),
                'bookmarksCount' => (int) ($this->bookmarks_count ?? $this->bookmarks()->count()),
                'followersCount' => 0,
                'followingCount' => 0,
            ],
            'email_verified_at' => optional($this->email_verified_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
