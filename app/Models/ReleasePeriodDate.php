<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleasePeriodDate extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'release_period_id',
    ];

    public $timestamps = false;

    protected $dates = ['start_date', 'end_date'];
}
