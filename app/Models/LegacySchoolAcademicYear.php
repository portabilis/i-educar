<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolAcademicYearBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacySchoolAcademicYear
 *
 * @property int $ano
 *
 * @method static LegacySchoolAcademicYearBuilder query()
 */
class LegacySchoolAcademicYear extends LegacyModel
{
    use LegacyAttribute;

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_ano_letivo';

    public const CREATED_AT = 'data_cadastro';

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
        'year' => 'ano',
        'created_at' => 'data_cadastro'
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
    public function academicYearStages(): HasMany
    {
        return $this->hasMany(LegacyAcademicYearStage::class, 'escola_ano_letivo_id');
    }
}
