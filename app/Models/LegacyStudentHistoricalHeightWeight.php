<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStudentHistoricalHeightWeight extends Model
{
    public $table = 'pmieducar.aluno_historico_altura_peso';

    public $timestamps = false;

    public $casts = [
        'data_historico' => 'date',
    ];

    public $fillable = [
        'ref_cod_aluno',
        'data_historico',
        'altura',
        'peso',
    ];

    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }
}
