<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolAcademicYearBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public const CREATED_AT = null;

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected string $builder = LegacySchoolAcademicYearBuilder::class;

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
        'copia_dados_professor',
        'copia_dados_demais_servidores'
    ];

    protected function year(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ano
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }
}
