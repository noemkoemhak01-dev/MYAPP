<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardBreakdown extends Model
{
    use HasFactory;

    protected $fillable = [
        'dashboard_snapshot_id',
        'metric_type',
        'label',
        'value',
        'position',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(DashboardSnapshot::class);
    }
}
