<?php

namespace App\Models;

use App\Models\Builders\LegacyInstitutionBuilder;
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
     * Builder dos filtros
     *
     * @var string
     */
    protected string $builder = LegacyInstitutionBuilder::class;

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
        'orgao_regional',
        'data_base_remanejamento',
        'data_base_transferencia',
        'data_expiracao_reserva_vaga',
        'data_base_matricula',
        'data_fechamento',
        'data_educacenso',
    ];

    protected $casts = [
        'data_base_remanejamento' => 'date',
        'data_educacenso' => 'date',
    ];

    public array $legacy = [
        'id' => 'cod_instituicao',
        'name' => 'nm_instituicao',
    ];

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
        return (bool) $this->obrigar_campos_censo;
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
            get: fn () => (bool) $this->permitir_matricula_fora_periodo_letivo
        );
    }

    protected function address(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(', ', [
                $this->logradouro,
                $this->numero,
                $this->bairro,
            ]) . ' - ' . $this->cidade . ' - ' . $this->ref_sigla_uf . ' - CEP:' . $this->cep
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->telefone ? '(' . $this->ddd_telefone . ') ' . $this->telefone : '(##) ####-####' ;
            }
        );
    }

    protected function cellphone(): Attribute
    {
        return Attribute::make(
            get: function () {
                return '(##) #####-####' ;
            }
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
