<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolAcademicYearBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;

/**
 * LegacySchoolAcademicYear
 *
 * @method static LegacySchoolAcademicYearBuilder query()
 */
class LegacySchoolAcademicYear extends LegacyModel
{
    use LegacyAttribute;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_ano_letivo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = LegacySchoolAcademicYearBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'year' => 'ano'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ano',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'andamento',
        'data_exclusao',
        'ativo',
        'turmas_por_ano',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getYearAttribute(): int
    {
        return $this->ano;
    }
}
