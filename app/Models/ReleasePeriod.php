<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function schools()
    {
        return $this->belongsToMany(
            LegacySchool::class,
            'release_period_schools',
            'release_period_id',
            'school_id'
        );
    }

    public function periodDates()
    {
        return $this->hasMany(ReleasePeriodDate::class);
    }

    public function stageType()
    {
        return $this->belongsTo(LegacyStageType::class, 'stage_type_id');
    }

    public function getDatesArray()
    {
        $dates = $this->periodDates;

        $datesString = [];
        foreach ($dates as $date) {
            $datesString[] = $date->start_date->format('d/m/Y') . ' a ' . $date->end_date->format('d/m/Y');
        }

        return $datesString;
    }
}
