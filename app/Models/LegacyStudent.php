<?php

namespace App\Models;

use App\Models\Builders\LegacyStudentBuilder;
use App\Models\View\HistoricGradeYear;
use App\Traits\HasLegacyDates;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class LegacyStudent extends LegacyModel
{
    use LegacyAttribute;
    use HasLegacyDates;

    public const CREATED_AT = 'data_cadastro';

    public string $builder = LegacyStudentBuilder::class;

    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_idpes',
        'tipo_responsavel',
    ];

    public array $legacy = [
        'id' => 'cod_aluno',
        'person_id' => 'ref_idpes',
    ];

    public function individual(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'ref_idpes');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(LegacyDocument::class, 'ref_idpes');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->person->name
        );
    }

    protected function socialName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->individual->social_name ?? null
        );
    }

    protected function realName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->person->real_name
        );
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(LegacyRegistration::class, 'ref_cod_aluno');
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyPerson::class,
            'pmieducar.responsaveis_aluno',
            'ref_cod_aluno',
            'ref_idpes',
            'cod_aluno',
            'idpes'
        );
    }

    protected function guardianType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tipo_responsavel
        );
    }

    public function getGuardions(): Collection
    {
        return collect([
            $this->individual->mother,
            $this->individual->father
        ])->filter(fn ($person) => !empty($person) && $person->name !== 'NÃO REGISTRADO');
    }

    public function getGuardianName(): ?string
    {
        return match ($this->guardianType) {
            'm' => $this->individual->mother->name,
            'p' => $this->individual->father->name,
            'r' => $this->individual->responsible->name,
            'a' => $this->joinGuardionNames(),
            default => null
        };
    }

    public function getGuardianCpf()
    {
        return match ($this->guardianType) {
            'm' => $this->individual->mother->individual->cpf ?? 'não informado',
            'p' => $this->individual->father->individual->cpf ?? 'não informado',
            'r' => $this->individual->responsible->individual->cpf ?? 'não informado',
            'a' => $this->joinGuardionCpfs(),
            default => null
        };
    }

    private function joinGuardionCpfs(): ?string
    {
        $join = ($this->individual->mother->individual->cpf ?? 'não informado') . ', ' . ($this->individual->father->individual->cpf ?? 'não informado');

        return strlen($join) < 3 ? null : $join;
    }

    private function joinGuardionNames(): ?string
    {
        $join = $this->individual->mother->name . ', ' . $this->individual->father->name;

        return strlen($join) < 3 ? null : $join;
    }

    protected function inepNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->inep?->number
        );
    }

    protected function stateRegistrationId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->aluno_estado_id
        );
    }

    public function setStateRegistrationIdAttribute($value): void
    {
        $this->aluno_estado_id = $value;
    }

    public function inep(): HasOne
    {
        return $this->hasOne(StudentInep::class, 'cod_aluno', 'cod_aluno');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('aluno.ativo', 1);
    }

    public function scopeMale(Builder $query): Builder
    {
        return $query->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('sexo', 'M');
    }

    public function scopeFemale(Builder $query): Builder
    {
        return $query->join('cadastro.fisica', 'aluno.ref_idpes', '=', 'fisica.idpes')
            ->where('sexo', 'F');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(LegacyBenefit::class, 'pmieducar.aluno_aluno_beneficio', 'aluno_id', 'aluno_beneficio_id');
    }

    public function historicGradeYear(): HasMany
    {
        return $this->hasMany(HistoricGradeYear::class, 'cod_aluno', 'cod_aluno');
    }

    public function historicGradeYearNotDiversified(): HasMany
    {
        return $this->historicGradeYear()->whereRaw('(select max(unnest) from unnest(tipos_base)) != 2');
    }

    public function historicGradeYearDiversified(): HasMany
    {
        return $this->historicGradeYear()->whereRaw('(select max(unnest) from unnest(tipos_base)) = 2');
    }

    public function registration_transfer(): HasOne
    {
        return $this->hasOne(LegacyRegistration::class, 'ref_cod_aluno')->transfer();
    }

    public function workload1(): Attribute
    {
        return Attribute::make(
            get: function () {
                $maxWorkload = $this->historicGradeYearNotDiversified->max('carga_horaria1');
                if ($maxWorkload !== null) {
                    return $maxWorkload;
                }

                $sumWorkload = $this->historicGradeYearNotDiversified->sum('chd1');
                if ($sumWorkload > 0) {
                    return $sumWorkload;
                }

                return '-';
            }
        );
    }

    public function workload2(): Attribute
    {
        return Attribute::make(
            get: function () {
                $maxWorkload = $this->historicGradeYearNotDiversified->max('carga_horaria2');
                if ($maxWorkload !== null) {
                    return $maxWorkload;
                }

                $sumWorkload = $this->historicGradeYearNotDiversified->sum('chd2');
                if ($sumWorkload > 0) {
                    return $sumWorkload;
                }

                return '-';
            }
        );
    }

    public function workload3(): Attribute
    {
        return Attribute::make(
            get: function () {
                $maxWorkload = $this->historicGradeYearNotDiversified->max('carga_horaria3');
                if ($maxWorkload !== null) {
                    return $maxWorkload;
                }

                $sumWorkload = $this->historicGradeYearNotDiversified->sum('chd3');
                if ($sumWorkload > 0) {
                    return $sumWorkload;
                }

                return '-';
            }
        );
    }

    public function workload4(): Attribute
    {
        return Attribute::make(
            get: function () {
                $maxWorkload = $this->historicGradeYearNotDiversified->max('carga_horaria4');
                if ($maxWorkload !== null) {
                    return $maxWorkload;
                }

                $sumWorkload = $this->historicGradeYearNotDiversified->sum('chd4');
                if ($sumWorkload > 0) {
                    return $sumWorkload;
                }

                return '-';
            }
        );
    }

    public function workload5(): Attribute
    {
        return Attribute::make(
            get: function () {
                $maxWorkload = $this->historicGradeYearNotDiversified->max('carga_horaria5');
                if ($maxWorkload !== null) {
                    return $maxWorkload;
                }

                $sumWorkload = $this->historicGradeYearNotDiversified->sum('chd5');
                if ($sumWorkload > 0) {
                    return $sumWorkload;
                }

                return '-';
            }
        );
    }
}
