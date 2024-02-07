<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolBuilder;
use App\Models\View\SchoolData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacySchool
 *
 * @property string            $name
 * @property LegacyInstitution $institution
 *
 * @method static LegacySchoolBuilder query()
 */
class LegacySchool extends LegacyModel
{
    public const CREATED_AT = 'data_cadastro';

    /**
     * @var string
     */
    protected $table = 'pmieducar.escola';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola';

    /**
     * Builder dos filtros
     */
    protected string $builder = LegacySchoolBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public array $legacy = [
        'id' => 'cod_escola',
        'person_id' => 'ref_idpes',
        'name' => 'fantasia',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'ref_cod_instituicao',
        'sigla',
        'data_cadastro',
        'data_exclusao',
        'ref_idpes',
        'ativo',
        'orgao_vinculado_escola',
        'situacao_funcionamento',
        'zona_localizacao',
        'localizacao_diferenciada',
        'dependencia_administrativa',
        'mantenedora_escola_privada',
        'categoria_escola_privada',
        'conveniada_com_poder_publico',
        'cnpj_mantenedora_principal',
        'regulamentacao',
        'esfera_administrativa',
        'unidade_vinculada_outra_instituicao',
        'inep_escola_sede',
        'codigo_ies',
        'qtd_vice_diretor',
        'qtd_orientador_comunitario',
        'qtd_tradutor_interprete_libras_outro_ambiente',
        'latitude',
        'longitude',
        'predio_compartilhado_outra_escola',
        'educacao_indigena',
        'compartilha_espacos_atividades_integracao',
        'usa_espacos_equipamentos_atividades_regulares',
        'exame_selecao_ingresso',
        'ref_idpes_gestor',
        'cargo_gestor',
        'nao_ha_funcionarios_para_funcoes',
        'formas_contratacao_parceria_escola_secretaria_municipal',
        'formas_contratacao_parceria_escola_secretaria_estadual',
        'poder_publico_parceria_convenio',
        'qtd_tradutor_interprete_libras_outro_ambiente',
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_escola,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->person->nome ?? $this->organization?->fantasia,
        );
    }

    /**
     * Relacionamento com a instituição.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * Anos letivos
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(LegacySchoolAcademicYear::class, 'ref_cod_escola');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyCourse::class,
            'pmieducar.escola_curso',
            'ref_cod_escola',
            'ref_cod_curso'
        )->withPivot('ativo', 'anos_letivos');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(LegacyOrganization::class, 'ref_idpes');
    }

    public function inep(): HasOne
    {
        return $this->hasOne(SchoolInep::class, 'cod_escola', 'cod_escola');
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyGrade::class,
            'pmieducar.escola_serie',
            'ref_cod_escola',
            'ref_cod_serie'
        )->withPivot('ativo', 'anos_letivos', 'bloquear_enturmacao_sem_vagas');
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_escola');
    }

    public function schoolUsers(): HasMany
    {
        return $this->hasMany(LegacyUserSchool::class, 'ref_cod_escola', 'cod_escola');
    }

    public function data(): BelongsTo
    {
        return $this->belongsTo(SchoolData::class, 'cod_escola');
    }

    public function schoolManagers(): HasMany
    {
        return $this->hasMany(SchoolManager::class, 'school_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola');
    }

    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(
            Place::class,
            'person_has_place',
            'person_id',
            'place_id',
            'ref_idpes',
        );
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes_gestor');
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes_secretario_escolar');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(LegacyRegistration::class, 'ref_ref_cod_escola');
    }
}
