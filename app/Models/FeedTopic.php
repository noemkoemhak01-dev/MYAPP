<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeedTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
        'description',
        'is_featured',
        'trend_score',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (FeedTopic $topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->name);
            }
        });
    }
}
