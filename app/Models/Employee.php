<?php

namespace App\Models;

use App\Models\Builders\EmployeeBuilder;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use LegacyAttribute;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.servidor';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_servidor';

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = EmployeeBuilder::class;

    protected $fillable = [
        'cod_servidor',
        'ref_cod_instituicao',
        'data_cadastro',
        'carga_horaria',
    ];

    /**
     * @return BelongsTo
     */
    public function inep()
    {
        return $this->belongsTo(EmployeeInep::class, 'cod_servidor', 'cod_servidor');
    }

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->cod_servidor;
    }

    /**
     * Servidor alocação
     *
     * @return HasMany
     */
    public function employeeAllocations(): HasMany
    {
        return $this->hasMany(EmployeeAllocation::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * Servidor função
     *
     * @return HasMany
     */
    public function employeeRoles(): HasMany
    {
        return $this->hasMany(LegacyEmployeeRole::class, 'ref_cod_servidor');
    }

    /**
     * Instituição
     *
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(LegacyInstitution::class, 'ref_cod_instituicao');
    }

    /**
     * Pessoa física
     *
     * @return BelongsTo
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'cod_servidor');
    }

    /**
     * @return BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(
            LegacySchool::class,
            'pmieducar.servidor_alocacao',
            'ref_cod_servidor',
            'ref_cod_escola'
        )->withPivot('ref_ref_cod_instituicao', 'ano')
            ->where('servidor_alocacao.ativo', 1);
    }

    /**
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_servidor');
    }

    /**
     * @return BelongsTo
     */
    public function schoolingDegree()
    {
        return $this->belongsTo(LegacySchoolingDegree::class, 'ref_idesco');
    }

    public function graduations()
    {
        return $this->hasMany(EmployeeGraduation::class, 'employee_id');
    }

    /**
     * @return BelongsToMany
     */
    public function disciplines()
    {
        return $this->belongsToMany(
            LegacyDiscipline::class,
            'pmieducar.servidor_disciplina',
            'ref_cod_servidor',
            'ref_cod_disciplina'
        )->withPivot('ref_ref_cod_instituicao', 'ref_cod_curso');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('servidor.ativo', 1);
    }

    public function scopeProfessor(Builder $query): Builder
    {
        return $query->join('pmieducar.servidor_funcao', 'servidor_funcao.ref_cod_servidor', '=', 'servidor.cod_servidor')
            ->join('pmieducar.funcao', 'funcao.cod_funcao', '=', 'servidor_funcao.ref_cod_funcao')
            ->where('funcao.professor', 1);
    }

    public function scopeLastYear(Builder $query): Builder
    {
        return $query->join('pmieducar.servidor_alocacao', 'servidor.cod_servidor', '=', 'servidor_alocacao.ref_cod_servidor')
            ->where('servidor_alocacao.ano', date('Y') - 1);
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->join('pmieducar.servidor_alocacao', 'servidor.cod_servidor', '=', 'servidor_alocacao.ref_cod_servidor')
            ->where('servidor_alocacao.ano', date('Y'));
    }
}
