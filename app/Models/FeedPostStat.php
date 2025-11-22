<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPostStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_post_id',
        'recorded_for',
        'views',
        'likes',
        'comments',
        'shares',
        'saves',
        'meta',
    ];

    protected $casts = [
        'recorded_for' => 'date',
        'meta' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'feed_post_id');
    }
}
