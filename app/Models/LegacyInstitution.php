<?php

namespace App\Models;

use App\Services\RelocationDate\RelocationDateProvider;
use App\Traits\HasLegacyDates;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyInstitution
 *
 * @property string   $name            Nome da instituição
 * @property string   $city            Noda da cidade da instituição
 * @property string   $state           Sigla do estado da instituição
 * @property DateTime $relocation_date Data base para remanejamento
 * @property DateTime $educacenso_date Data de corte do Educacenso
 */
class LegacyInstitution extends LegacyModel implements RelocationDateProvider
{
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'pmieducar.instituicao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_instituicao';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'ref_idtlog',
        'ref_sigla_uf',
        'cep',
        'cidade',
        'bairro',
        'logradouro',
        'nm_responsavel',
        'nm_instituicao',
        'orgao_regional'
    ];

    protected $casts = [
        'data_base_remanejamento' => 'date',
        'data_educacenso' => 'date',
    ];

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', 1);
    }

    /**
     * @return HasOne
     */
    public function generalConfiguration(): HasOne
    {
        return $this->hasOne(LegacyGeneralConfiguration::class, 'ref_cod_instituicao', 'cod_instituicao');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nm_instituicao
        );
    }

    protected function city(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cidade
        );
    }

    protected function state(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_sigla_uf
        );
    }

    protected function relocationDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data_base_remanejamento
        );
    }

    protected function educacensoDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data_educacenso
        );
    }

    /**
     * Indica se os campos do Censo são obrigatórios.
     *
     * @return bool
     */
    public function isMandatoryCensoFields(): bool
    {
        return (bool)$this->obrigar_campos_censo;
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_instituicao
        );
    }

    protected function allowRegistrationOutAcademicYear(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool)$this->permitir_matricula_fora_periodo_letivo
        );
    }

    /**
     * @return HasMany
     */
    public function schools(): HasMany
    {
        return $this->hasMany(LegacySchool::class, 'ref_cod_instituicao', 'cod_instituicao');
    }

    /**
     * Regras de avaliação
     *
     * @return HasMany
     */
    public function evaluationRules(): HasMany
    {
        return $this->hasMany(LegacyEvaluationRule::class, 'instituicao_id');
    }

    public function getRelocationDate(): string|null
    {
        return $this->relocationDate?->format('Y-m-d');
    }
}
