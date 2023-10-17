<?php

namespace App\Models;

use App\Models\Builders\EmployeeBuilder;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends LegacyModel
{
    use HasInstitution;
    use HasLegacyDates;
    use LegacyAttribute;

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
     */
    protected string $builder = EmployeeBuilder::class;

    protected $fillable = [
        'cod_servidor',
        'carga_horaria',
    ];

    public array $legacy = [
        'id' => 'cod_servidor',
        'workload' => 'carga_horaria',
    ];

    /**
     * @return BelongsTo
     */
    public function inep()
    {
        return $this->belongsTo(EmployeeInep::class, 'cod_servidor', 'cod_servidor');
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_servidor,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->person->nome
        );
    }

    /**
     * Servidor alocação
     */
    public function employeeAllocations(): HasMany
    {
        return $this->hasMany(EmployeeAllocation::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * Servidor função
     */
    public function employeeRoles(): HasMany
    {
        return $this->hasMany(LegacyEmployeeRole::class, 'ref_cod_servidor');
    }

    /**
     * Pessoa física
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'cod_servidor');
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacySchool::class,
            'pmieducar.servidor_alocacao',
            'ref_cod_servidor',
            'ref_cod_escola'
        )->withPivot('ref_ref_cod_instituicao', 'ano')
            ->where('servidor_alocacao.ativo', 1);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_servidor');
    }

    public function schoolingDegree(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolingDegree::class, 'ref_idesco');
    }

    public function graduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'employee_id');
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyDiscipline::class,
            'pmieducar.servidor_disciplina',
            'ref_cod_servidor',
            'ref_cod_disciplina'
        )->withPivot('ref_ref_cod_instituicao', 'ref_cod_curso', 'ref_cod_funcao');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyCourse::class,
            'pmieducar.servidor_curso_ministra',
            'ref_cod_servidor',
            'ref_cod_curso'
        )->withPivot('ref_ref_cod_instituicao');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('servidor.ativo', 1);
    }

    public function scopeProfessor(Builder $query, $onlyTeacher = true): Builder
    {
        return $query->join('pmieducar.servidor_funcao', 'servidor_funcao.ref_cod_servidor', '=', 'servidor.cod_servidor')
            ->join('pmieducar.funcao', 'funcao.cod_funcao', '=', 'servidor_funcao.ref_cod_funcao')
            ->where('funcao.professor', (int) $onlyTeacher);
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

    public function withdrawals(): HasMany
    {
        return $this->hasMany(EmployeeWithdrawal::class, 'ref_cod_servidor', 'cod_servidor');
    }
}
