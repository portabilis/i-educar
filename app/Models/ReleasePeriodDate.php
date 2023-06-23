<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReleasePeriodDate extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'release_period_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function releasePeriod(): BelongsTo
    {
        return $this->belongsTo(ReleasePeriod::class);
    }
}
