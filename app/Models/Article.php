<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category_id',
        'author_id',
        'image_url',
        'published_at',
        'views',
        'read_time',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
        'read_time' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            if (empty($article->published_at)) {
                $article->published_at = now();
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    public function scopeTrending($query)
    {
        return $query->published()
                     ->orderBy('views', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->published()
                     ->orderBy('published_at', 'desc');
    }

    // Accessors
    public function getViewsFormattedAttribute()
    {
        if ($this->views >= 1000000) {
            return round($this->views / 1000000, 1) . 'M';
        } elseif ($this->views >= 1000) {
            return round($this->views / 1000, 1) . 'K';
        }
        return (string) $this->views;
    }

    public function getTimeAgoAttribute()
    {
        $diff = Carbon::now()->diff($this->published_at);
        
        if ($diff->days > 0) {
            return $diff->days . 'd ago';
        } elseif ($diff->h > 0) {
            return $diff->h . 'h ago';
        } elseif ($diff->i > 0) {
            return $diff->i . 'm ago';
        }
        return 'Just now';
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function isBookmarkedBy($userId)
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }
}