<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LegacySchoolStage
 *
 * @package App\Models
 * todo Verificar duplicidade com LegacySchoolStage
 */
class LegacyAcademicYearStage extends Model
{
    use DateSerializer;

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
    protected $dates = [
        'data_inicio',
        'data_fim',
    ];

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
