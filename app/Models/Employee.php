<?php

namespace App\Models;

use App\Models\Builders\EmployeeBuilder;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int $cod_servidor
 * @property LegacyPerson $person
 * @property array<int, string> $fillable
 * @property int $curso_formacao_continuada
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

    protected function continuedTrainingCourse(): Attribute
    {
        return Attribute::make(
            get: fn () => collect(transformStringFromDBInArray($this->curso_formacao_continuada) ?? [])->map(fn ($course) => match ($course) {
                '1' => 'Creche (0 a 3 anos)',
                '2' => 'Pré-escola (4 e 5 anos)',
                '3' => 'Anos iniciais do ensino fundamental',
                '4' => 'Anos finais do ensino fundamental',
                '5' => 'Ensino médio',
                '6' => 'Educação de jovens e adultos',
                '7' => 'Educação especial',
                '8' => 'Educação indígena',
                '9' => 'Educação do campo',
                '10' => 'Educação ambiental',
                '11' => 'Educação em direitos humanos',
                '18' => 'Educação bilíngue de surdos',
                '19' => 'Educação e Tecnologia de Informação e Comunicação (TIC)',
                '12' => 'Gênero e diversidade sexual',
                '13' => 'Direitos de criança e adolescente',
                '14' => 'Educação para as relações étnico-raciais e História e cultura Afro-Brasileira e Africana',
                '17' => 'Gestão Escolar',
                '15' => 'Outros',
                default => null
            })->filter(),
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
     * @return HasOne<EmployeeAllocation, $this>
     */
    public function employeeAllocation(): HasOne
    {
        return $this->hasOne(EmployeeAllocation::class, 'ref_cod_servidor', 'cod_servidor');
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
     * @return HasMany<EmployeePosgraduate, $this>
     */
    public function posGraduations(): HasMany
    {
        return $this->hasMany(EmployeePosgraduate::class, 'employee_id');
    }

    public function place(): HasOneThrough // @phpstan-ignore-line
    {
        // @phpstan-ignore-next-line
        return $this->hasOneThrough(
            Place::class,
            PersonHasPlace::class,
            'person_id',
            'id',
            'cod_servidor',
            'place_id'
        )->orderBy('type');
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
