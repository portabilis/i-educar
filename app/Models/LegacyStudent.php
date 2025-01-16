<?php

namespace App\Models;

use Ankurk91\Eloquent\HasBelongsToOne;
use Ankurk91\Eloquent\Relations\BelongsToOne;
use App\Events\StudentCreated;
use App\Models\Builders\LegacyStudentBuilder;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property string $url_laudo_medico
 * @property LegacyPerson $person
 * @property LegacyIndividual $individual
 * @property StudentInep $inep
 * @property string $tipo_responsavel
 * @property string $guardianType
 * @property string $name
 * @property mixed $aluno_estado_id
 * @property int $cod_aluno
 */
class LegacyStudent extends LegacyModel
{
    use HasBelongsToOne;

    /** @use HasBuilder<LegacyStudentBuilder> */
    use HasBuilder;

    use HasLegacyDates;

    public const CREATED_AT = 'data_cadastro';

    protected static string $builder = LegacyStudentBuilder::class;

    protected $table = 'pmieducar.aluno';

    protected $primaryKey = 'cod_aluno';

    protected $dispatchesEvents = [
        'created' => StudentCreated::class,
    ];

    protected $fillable = [
        'ref_idpes',
        'tipo_responsavel',
        'codigo_sistema',
        'ativo',
        'ref_usuario_cad',
    ];

    /**
     * @var array<string, string>
     */
    public array $legacy = [
        'id' => 'cod_aluno',
        'person_id' => 'ref_idpes',
    ];

    /**
     * @return BelongsTo<LegacyIndividual, $this>
     */
    public function individual(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'ref_idpes');
    }

    /**
     * @return BelongsTo<LegacyDocument, $this>
     */
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

    protected function birthdate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->individual->data_nasc,
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

    /**
     * @return HasOne<LegacyIndividualPicture, $this>
     */
    public function picture(): HasOne
    {
        return $this->hasOne(LegacyIndividualPicture::class, 'idpes', 'ref_idpes');
    }

    /**
     * @return BelongsTo<LegacyPerson, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'ref_idpes');
    }

    /**
     * @return BelongsToMany<LegacyDeficiency, $this>
     */
    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            LegacyDeficiency::class,
            'cadastro.fisica_deficiencia',
            'ref_idpes',
            'ref_cod_deficiencia',
            'ref_idpes',
            'cod_deficiencia'
        );
    }

    public function deficiency(): BelongsToOne
    {
        return $this->belongsToOne(
            LegacyDeficiency::class,
            'cadastro.fisica_deficiencia',
            'ref_idpes',
            'ref_cod_deficiencia',
            'ref_idpes',
            'cod_deficiencia'
        );
    }

    /**
     * @return HasMany<LegacyRegistration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(LegacyRegistration::class, 'ref_cod_aluno');
    }

    /**
     * @return HasOne<LegacyRegistration, $this>
     */
    public function lastRegistration(): HasOne
    {
        // @phpstan-ignore-next-line
        return $this->hasOne(LegacyRegistration::class, 'ref_cod_aluno')
            ->orderByDesc('ano')
            ->active();
    }

    protected function guardianType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tipo_responsavel
        );
    }

    public function hasReport(): bool
    {
        return $this->url_laudo_medico !== null && $this->url_laudo_medico !== '[]';
    }

    /**
     * @return Collection<int, LegacyPerson>
     */
    public function getGuardions(): Collection
    {
        return collect([
            $this->individual->mother,
            $this->individual->father,
        ])->filter(fn ($person) => !empty($person) && $person->name !== 'NÃO REGISTRADO'); // @phpstan-ignore-line
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

    public function getGuardianCpf(): ?string
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
            get: fn () => $this->inep?->number // @phpstan-ignore-line
        );
    }

    protected function stateRegistrationId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->aluno_estado_id
        );
    }

    public function setStateRegistrationIdAttribute(mixed $value): void
    {
        $this->aluno_estado_id = $value;
    }

    /**
     * @return HasOne<StudentInep, $this>
     */
    public function inep(): HasOne
    {
        return $this->hasOne(StudentInep::class, 'cod_aluno', 'cod_aluno');
    }

    /**
     * @return BelongsToMany<LegacyBenefit, $this>
     */
    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(LegacyBenefit::class, 'pmieducar.aluno_aluno_beneficio', 'aluno_id', 'aluno_beneficio_id');
    }

    /**
     * @return HasMany<LegacySchoolHistory, $this>
     */
    public function schoolHistories(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(LegacySchoolHistory::class, 'ref_cod_aluno', 'cod_aluno')->active();
    }

    /**
     * @return HasMany<LegacySchoolHistoryDiscipline, $this>
     */
    public function schoolHistoryDisciplines(): HasMany
    {
        return $this->hasMany(LegacySchoolHistoryDiscipline::class, 'ref_ref_cod_aluno', 'cod_aluno');
    }

    /**
     * @return HasOne<LegacyRegistration, $this>
     */
    public function registration_transfer(): HasOne
    {
        // @phpstan-ignore-next-line
        return $this->hasOne(LegacyRegistration::class, 'ref_cod_aluno')->transfer();
    }

    /**
     * @return HasOne<LegacyStudentMedicalRecord, $this>
     */
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(LegacyStudentMedicalRecord::class, 'ref_cod_aluno');
    }

    /**
     * @return HasMany<LegacyStudentMedicalRecord, $this>
     */
    public function historicalHeightWeight(): HasMany
    {
        return $this->hasMany(LegacyStudentHistoricalHeightWeight::class, 'ref_cod_aluno');
    }
}
