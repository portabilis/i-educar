<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReleasePeriod extends Model
{
    protected $fillable = [
        'year',
        'stage_type_id',
        'stage',
    ];

    /**
     * The roles that belong to the user.
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

    public function periodDates(): HasMany
    {
        return $this->hasMany(ReleasePeriodDate::class);
    }

    public function stageType(): BelongsTo
    {
        return $this->belongsTo(LegacyStageType::class, 'stage_type_id');
    }

    public function getDatesArray(): array
    {
        $dates = $this->periodDates;

        $datesString = [];
        foreach ($dates as $date) {
            $datesString[] = $date->start_date->format('d/m/Y') . ' a ' . $date->end_date->format('d/m/Y');
        }

        return $datesString;
    }
}
