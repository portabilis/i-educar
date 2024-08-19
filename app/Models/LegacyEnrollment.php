<?php

namespace App\Models;

use App\Casts\LegacyArray;
use App\Models\Builders\LegacyEnrollmentBuilder;
use App\Support\Database\DateSerializer;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * LegacyEnrollment
 *
 * @property int                $id
 * @property int                $registration_id
 * @property int                $school_class_id
 * @property int                $etapa_educacenso
 * @property int                $ref_cod_turma
 * @property int                $ref_cod_matricula
 * @property string             $studentName
 * @property DateTime           $date
 * @property LegacyRegistration $registration
 * @property LegacySchoolClass  $schoolClass
 * @property Carbon             $data_enturmacao
 * @property Carbon             $data_exclusao
 */
class LegacyEnrollment extends LegacyModel
{
    use DateSerializer;

    /** @use HasBuilder<LegacyEnrollmentBuilder> */
    use HasBuilder;

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = 'updated_at';

    protected $table = 'pmieducar.matricula_turma';

    protected $primaryKey = 'id';

    protected static string $builder = LegacyEnrollmentBuilder::class;

    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_turma',
        'sequencial',
        'ref_usuario_cad',
        'data_enturmacao',
        'sequencial_fechamento',
        'remanejado_mesma_turma',
        'ativo',
        'tipo_itinerario',
        'composicao_itinerario',
        'curso_itinerario',
        'itinerario_concomitante',
        'etapa_educacenso',
        'cod_curso_profissional',
        'desconsiderar_educacenso',
        'transferido',
        'remanejado',
        'reclassificado',
        'abandono',
        'falecido',
    ];

    protected $casts = [
        'tipo_itinerario' => LegacyArray::class,
        'composicao_itinerario' => LegacyArray::class,
        'data_enturmacao' => 'date',
        'data_exclusao' => 'date',
        'desconsiderar_educacenso' => 'boolean',
    ];

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data_enturmacao,
        );
    }

    protected function dateDeparted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data_exclusao,
        );
    }

    protected function schoolClassId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_turma,
        );
    }

    protected function registrationId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ref_cod_matricula,
        );
    }

    protected function studentName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->registration->student->person->nome ?? null,
        );
    }

    /**
     * Relação com a matrícula.
     *
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }

    /**
     * Relação com a turma.
     *
     * @return BelongsTo<LegacySchoolClass, $this>
     */
    public function schoolClass()
    {
        return $this->belongsTo(LegacySchoolClass::class, 'ref_cod_turma');
    }

    /**
     * @return BelongsTo<LegacyPeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(LegacyPeriod::class, 'turno_id')->withDefault();
    }

    /**
     * @return HasOne<LegacyRegistrationScore, $this>
     */
    public function registrationScore(): HasOne
    {
        return $this->hasOne(LegacyRegistrationScore::class, 'matricula_id', 'ref_cod_matricula');
    }

    /**
     * Relação com servidor.
     *
     * @return BelongsTo<LegacyUser, $this>
     */
    public function createdBy()
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_cad');
    }

    /**
     * Relação com servidor.
     *
     * @return BelongsTo<LegacyUser, $this>
     */
    public function updatedBy()
    {
        return $this->belongsTo(LegacyUser::class, 'ref_usuario_exc');
    }

    public function getStudentId(): int
    {
        return $this->registration->student->cod_aluno;
    }

    /**
     * @return HasOne<EnrollmentInep, $this>
     */
    public function inep(): HasOne
    {
        return $this->hasOne(EnrollmentInep::class, 'matricula_turma_id');
    }
}
