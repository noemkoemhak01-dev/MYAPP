<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_post_id',
        'media_type',
        'path',
        'position',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'feed_post_id');
    }
}
