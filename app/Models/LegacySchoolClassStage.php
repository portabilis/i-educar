<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;

class LegacySchoolClassStage extends Model
{
    use DateSerializer;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_turma';

    /**
     * @var array
     */
    protected $dates = [
        'data_inicio',
        'data_fim',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_turma',
        'ref_cod_modulo',
        'sequencial',
        'data_inicio',
        'data_fim',
        'dias_letivos',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
