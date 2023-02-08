<?php

namespace App\Models;

use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @package App\Models
 */
class LegacyAcademicYearStage extends Model
{
    use DateSerializer;

    /**
     * @var string
     */
    protected $table = 'pmieducar.ano_letivo_modulo';

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
        'escola_ano_letivo_id'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function module(): BelongsTo
    {
        return $this->belongsTo(LegacyStageType::class, 'ref_cod_modulo');
    }

    public function schoolAcademicYear(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolAcademicYear::class, 'ref_ref_cod_escola', 'ref_cod_escola');
    }
}
