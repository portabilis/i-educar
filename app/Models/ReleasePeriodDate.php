<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class ReleasePeriodDate extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'release_period_id',
    ];

    public $timestamps = false;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * @return BelongsTo<ReleasePeriod, $this>
     */
    public function releasePeriod(): BelongsTo
    {
        return $this->belongsTo(ReleasePeriod::class);
    }
}
