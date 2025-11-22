<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class FeedPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'topic',
        'category',
        'type',
        'excerpt',
        'content',
        'cover_image',
        'video_url',
        'is_featured',
        'is_trending',
        'is_published',
        'published_at',
        'view_count',
        'like_count',
        'comment_count',
        'share_count',
        'meta',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_published' => 'boolean',
        'meta' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (FeedPost $post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title ?? Str::random(8));
            }
        });

        static::updating(function (FeedPost $post) {
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(FeedPostMedia::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class)->latest();
    }

    public function stats(): HasMany
    {
        return $this->hasMany(FeedPostStat::class);
    }

    public function latestStat(): HasOne
    {
        return $this->hasOne(FeedPostStat::class)->latestOfMany();
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeTrending($query)
    {
        return $query->published()->where('is_trending', true);
    }

    public function scopeOfType($query, ?string $type)
    {
        return $type ? $query->where('type', $type) : $query;
    }
}
