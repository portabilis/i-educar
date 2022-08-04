<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolAcademicYearBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Model;

/**
 * LegacySchoolAcademicYear
 *
 * @method static LegacySchoolAcademicYearBuilder query()
 */
class LegacySchoolAcademicYear extends Model
{
    use LegacyAttribute;

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
    public $legacy = [
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
        'data_cadastro',
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
