<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LegacyCalendarDayNote extends Pivot
{
    public $incrementing = false;

    public $table = 'pmieducar.calendario_dia_anotacao';

    public $timestamps = false;

    protected $fillable = [
        'ref_dia',
        'ref_mes',
        'ref_ref_cod_calendario_ano_letivo',
        'ref_cod_calendario_anotacao',
    ];

    public function calendarNote()
    {
        return $this->belongsTo(LegacyCalendarNote::class, 'ref_cod_calendario_anotacao');
    }
}
