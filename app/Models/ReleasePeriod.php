<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 * @property HasMany $periodDates
 */
class ReleasePeriod extends Model
{
    protected $fillable = [
        'year',
        'stage_type_id',
        'stage',
    ];

    /**
     * @return BelongsToMany<LegacySchool, $this>
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacySchool::class,
            'release_period_schools',
            'release_period_id',
            'school_id'
        );
    }

    /**
     * @return HasMany<ReleasePeriodDate, $this>
     */
    public function periodDates(): HasMany
    {
        return $this->hasMany(ReleasePeriodDate::class);
    }

    /**
     * @return BelongsTo<LegacyStageType, $this>
     */
    public function stageType(): BelongsTo
    {
        return $this->belongsTo(LegacyStageType::class, 'stage_type_id');
    }

    /**
     * @return array<int, string>
     */
    public function getDatesArray(): array
    {
        $dates = $this->periodDates;

        $datesString = [];
        /** @phpstan-ignore-next-line  */
        foreach ($dates as $date) {
            $datesString[] = $date->start_date->format('d/m/Y') . ' a ' . $date->end_date->format('d/m/Y');
        }

        return $datesString;
    }
}
