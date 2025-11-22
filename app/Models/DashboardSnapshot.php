<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'range_type',
        'range_start',
        'range_end',
        'total_views',
        'visits',
        'new_users',
        'active_users',
        'views_change',
        'visits_change',
        'new_users_change',
        'active_users_change',
        'meta',
    ];

    protected $casts = [
        'range_start' => 'date',
        'range_end' => 'date',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breakdowns(): HasMany
    {
        return $this->hasMany(DashboardBreakdown::class);
    }
}
