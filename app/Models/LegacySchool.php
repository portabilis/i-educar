<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacySchool
 *
 * @property string            $name
 * @property LegacyInstitution $institution
 */
class LegacySchool extends Model
{
    use LegacyAttribute;

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
     *
     * @var string
     */
    protected $builder = LegacySchoolBuilder::class;

    /**
     * Atributos legados para serem usados nas queries
     *
     * @var string[]
     */
    public $legacy = [
        'id' => 'cod_escola',
        'name' => 'fantasia'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'cod_escola',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'ref_cod_instituicao',
        'ref_cod_escola_rede_ensino',
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
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_escola;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->organization->fantasia ?? null;
    }

    /**
     * Relacionamento com a instituição.
     *
     * @return BelongsTo
     */
    public function institution()
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * Anos letivos
     *
     * @return HasMany
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(LegacySchoolAcademicYear::class,'ref_cod_escola');
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    /**
     * @return BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany(
            LegacyCourse::class,
            'pmieducar.escola_curso',
            'ref_cod_escola',
            'ref_cod_curso'
        )->withPivot('ativo', 'anos_letivos');
    }

    /**
     * @return BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(LegacyOrganization::class, 'ref_idpes');
    }

    /**
     * @return HasOne
     */
    public function inep()
    {
        return $this->hasOne(SchoolInep::class, 'cod_escola', 'cod_escola');
    }

    /**
     * @return BelongsToMany
     */
    public function grades()
    {
        return $this->belongsToMany(
            LegacyLevel::class,
            'pmieducar.escola_serie',
            'ref_cod_escola',
            'ref_cod_serie'
        )->withPivot('ativo', 'anos_letivos', 'bloquear_enturmacao_sem_vagas');
    }

    /**
     * @return HasMany
     */
    public function schoolClasses()
    {
        return $this->hasMany(LegacySchoolClass::class, 'ref_ref_cod_escola');
    }

    /**
     * @return HasMany
     */
    public function schoolManagers()
    {
        return $this->hasMany(SchoolManager::class, 'school_id');
    }

    public function stages()
    {
        return $this->hasMany(LegacySchoolStage::class, 'ref_ref_cod_escola');
    }

    /**
     * Filtra por Ativo
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeActive(Builder $builder): void
    {
        $builder->where('escola.ativo', 1);
    }

    /**
     * Realiza a junçao com organização
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeJoinOrganization(Builder $builder): void
    {
        $builder->join('cadastro.juridica','idpes','ref_idpes');
    }

    /**
     * Filtra por Instituição
     *
     * @param Builder $query
     * @param int $institution
     * @return void
     */
    public function scopeWhereInstitution(Builder $query, int $institution): void
    {
        $query->where('ref_cod_instituicao', $institution);
    }

    /**
     * Ordena por nome
     *
     * @param Builder $query
     * @param string $direction
     * @return void
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): void
    {
        $query->joinOrganization();
        $query->orderBy('fantasia',$direction);
    }
}
