<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LegacySchoolStage
 * @package App\Models
 * todo Verificar duplicidade com LegacyAcademicYearStage
 */
class LegacySchoolStage extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.ano_letivo_modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_ano',
        'ref_ref_cod_escola',
        'sequencial',
        'ref_cod_modulo',
        'data_inicio',
        'data_fim',
        'dias_letivos',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
