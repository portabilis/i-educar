<?php

namespace App\Models;

use App\Models\Builders\LegacyCourseBuilder;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LegacyCourse
 *
 * @property string        $name
 * @property LegacyGrade[] $grades
 *
 * @method static LegacyCourseBuilder query()
 */
class LegacyCourse extends LegacyModel
{
    use HasLegacyDates;
    use LegacyAttribute;

    public const CREATED_AT = 'data_cadastro';

    /**
     * @var string
     */
    protected $table = 'pmieducar.curso';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_curso';

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacyCourseBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     */
    public array $legacy = [
        'id' => 'cod_curso',
        'name' => 'nm_curso',
        'is_standard_calendar' => 'padrao_ano_escolar',
        'steps' => 'qtd_etapas',
        'description' => 'descricao',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'ref_cod_tipo_regime',
        'ref_cod_nivel_ensino',
        'ref_cod_tipo_ensino',
        'nm_curso',
        'descricao',
        'sgl_curso',
        'qtd_etapas',
        'carga_horaria',
        'ref_cod_instituicao',
        'hora_falta',
        'ativo',
        'modalidade_curso',
        'padrao_ano_escolar',
        'multi_seriado',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'padrao_ano_escolar' => 'boolean',
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_curso,
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->descricao,
        );
    }

    protected function steps(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->qtd_etapas,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->description)) {
                    return $this->nm_curso;
                }

                return $this->nm_curso . ' (' . $this->description . ')';
            },
        );
    }

    protected function isStandardCalendar(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->padrao_ano_escolar,
        );
    }

    protected function hourAbsence(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->hora_falta
        );
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * Relacionamento com as series
     *
     * @return HasMany
     */
    public function grades()
    {
        return $this->hasMany(LegacyGrade::class, 'ref_cod_curso');
    }

    /**
     * Relaciona com  as escolas
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(LegacySchool::class, 'pmieducar.escola_curso', 'ref_cod_curso', 'ref_cod_escola')->wherePivot('ativo', 1);
    }

    public function educationType(): BelongsTo
    {
        return $this->belongsTo(LegacyEducationType::class, 'ref_cod_tipo_ensino');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(LegacyEducationLevel::class, 'ref_cod_nivel_ensino');
    }
}
