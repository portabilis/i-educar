<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequenciaInformacoes extends Model
{
/**
     * @var string
     */
    protected $table = 'modules.frequencia_informacoes';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'ref_frequencia',
        'dias_letivos',
        'dias_realizados',
        'dias_realizar',
        'ch',
        'aulas_realizadas',
        'aulas_realizar',
        'tipo_turma'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    use HasFactory;

}
