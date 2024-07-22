<?php

namespace App\Models;

use App\Models\Builders\EmployeeBuilder;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $cod_servidor
 * @property LegacyPerson $person
 * @property array<int, string> $fillable
 */
class Employee extends LegacyModel
{
    /** @use HasBuilder<EmployeeBuilder> */
    use HasBuilder;

    use HasInstitution;
    use HasLegacyDates;

    protected $table = 'pmieducar.servidor';

    protected $primaryKey = 'cod_servidor';

    protected static string $builder = EmployeeBuilder::class;

    protected $fillable = [
        'cod_servidor',
        'carga_horaria',
    ];

    /**
     * @var array<string, string>
     */
    public array $legacy = [
        'id' => 'cod_servidor',
        'workload' => 'carga_horaria',
    ];

    /**
     * @return BelongsTo<EmployeeInep, $this>
     */
    public function inep(): BelongsTo
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
     * @return HasMany<EmployeeAllocation, $this>
     */
    public function employeeAllocations(): HasMany
    {
        return $this->hasMany(EmployeeAllocation::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * @return HasMany<LegacyEmployeeRole, $this>
     */
    public function employeeRoles(): HasMany
    {
        return $this->hasMany(LegacyEmployeeRole::class, 'ref_cod_servidor');
    }

    /**
     * @return BelongsTo<LegacyIndividual, $this>
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'cod_servidor');
    }

    /**
     * @return BelongsToMany<LegacySchool, $this>
     */
    public function schools(): BelongsToMany
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsToMany(
            LegacySchool::class,
            'pmieducar.servidor_alocacao',
            'ref_cod_servidor',
            'ref_cod_escola'
        )->withPivot('ref_ref_cod_instituicao', 'ano')
            ->where('servidor_alocacao.ativo', 1);
    }

    /**
     * @return BelongsTo<LegacyPerson, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'cod_servidor');
    }

    /**
     * @return BelongsTo<LegacySchoolingDegree, $this>
     */
    public function schoolingDegree(): BelongsTo
    {
        return $this->belongsTo(LegacySchoolingDegree::class, 'ref_idesco');
    }

    /**
     * @return HasMany<EmployeeGraduation, $this>
     */
    public function graduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'employee_id');
    }

    /**
     * @return BelongsToMany<LegacyDiscipline, $this>
     */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyDiscipline::class,
            'pmieducar.servidor_disciplina',
            'ref_cod_servidor',
            'ref_cod_disciplina'
        )->withPivot('ref_ref_cod_instituicao', 'ref_cod_curso', 'ref_cod_funcao');
    }

    /**
     * @return BelongsToMany<LegacyCourse, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyCourse::class,
            'pmieducar.servidor_curso_ministra',
            'ref_cod_servidor',
            'ref_cod_curso'
        )->withPivot('ref_ref_cod_instituicao');
    }

    /** @phpstan-ignore-next-line  */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('servidor.ativo', 1);
    }

    /** @phpstan-ignore-next-line  */
    public function scopeProfessor(Builder $query, $onlyTeacher = true): Builder
    {
        /** @phpstan-ignore-next-line */
        return $query->join('pmieducar.servidor_funcao', 'servidor_funcao.ref_cod_servidor', '=', 'servidor.cod_servidor')
            ->join('pmieducar.funcao', 'funcao.cod_funcao', '=', 'servidor_funcao.ref_cod_funcao')
            ->where('funcao.professor', (int) $onlyTeacher);
    }

    /** @phpstan-ignore-next-line  */
    public function scopeLastYear(Builder $query): Builder
    {
        /** @phpstan-ignore-next-line */
        return $query->join('pmieducar.servidor_alocacao', 'servidor.cod_servidor', '=', 'servidor_alocacao.ref_cod_servidor')
            ->where('servidor_alocacao.ano', date('Y') - 1);
    }

    /** @phpstan-ignore-next-line */
    public function scopeCurrentYear(Builder $query): Builder
    {
        /** @phpstan-ignore-next-line  */
        return $query->join('pmieducar.servidor_alocacao', 'servidor.cod_servidor', '=', 'servidor_alocacao.ref_cod_servidor')
            ->where('servidor_alocacao.ano', date('Y'));
    }

    /**
     * @return HasMany<EmployeeWithdrawal, $this>
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(EmployeeWithdrawal::class, 'ref_cod_servidor', 'cod_servidor');
    }

    /**
     * @return HasMany<LegacySchoolClassTeacher, $this>
     */
    public function schoolClassTeachers(): HasMany
    {
        return $this->hasMany(LegacySchoolClassTeacher::class, 'servidor_id');
    }
}
