<?php

namespace App\Services;

use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Enrollment\NoVacancyException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyEnrollment;
use App\Models\LegacyUser;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class EnrollmentService
{
    /**
     * @var LegacyUser
     */
    private $user;

    /**
     * @param LegacyUser $user
     */
    public function __construct(LegacyUser $user)
    {
        $this->user = $user;
    }

    /**
     * Retorna a enturmação.
     *
`     * @param int $enrollment ID da enturmação
     *
     * @return LegacyEnrollment $enrollment
     *
     * @throws ModelNotFoundException
     */
    public function find($enrollment)
    {
        /** @var LegacyEnrollment $enrollment */
        $enrollment = LegacyEnrollment::findOrFail($enrollment);

        return $enrollment;
    }

    /**
     * Cancela uma enturmação.
     *
     * @param int      $enrollment ID da enturmação
     * @param DateTime $date       Data do cancelamento
     *
     * @return bool
     *
     * @throws PreviousCancellationDateException
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function cancelEnrollment($enrollment, $date)
    {
        $enrollment = $this->find($enrollment);

        if ($date < $enrollment->date) {
            throw new PreviousCancellationDateException($enrollment, $date);
        }

        $enrollment->ref_usuario_exc = $this->user->getKey();
        $enrollment->data_exclusao = $date;
        $enrollment->ativo = 0;

        return $enrollment->saveOrFail();
    }

    /**
     * @param LegacyRegistration $registration
     * @param LegacySchoolClass  $schoolClass
     * @param Carbon             $date
     *
     * @return LegacyEnrollment
     *
     * @throws NoVacancyException
     * @throws ExistsActiveEnrollmentException
     * @throws PreviousEnrollDateException
     */
    public function enroll(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        Carbon $date
    ) {
        if (empty($schoolClass->vacancies)) {
            throw new NoVacancyException($schoolClass);
        }

        $existsActiveEnrollment = $registration->enrollments()
            ->where('ativo', 1)
            ->where('ref_cod_turma', $schoolClass->id)
            ->count();

        if ($existsActiveEnrollment) {
            throw new ExistsActiveEnrollmentException($registration);
        }

        if ($registration->lastEnrollment && $registration->lastEnrollment->date_departed > $date) {
            throw new PreviousEnrollDateException($date, $registration->lastEnrollment);
        }

        /** @var LegacyEnrollment $enrollment */
        $enrollment = $registration->enrollments()->create([
            'ref_cod_turma' => $schoolClass->id,
            'sequencial' => $registration->enrollments()->max('sequencial') + 1,
            'ref_usuario_cad' => $this->user->getKey(),
            'data_cadastro' => Carbon::now(),
            'data_enturmacao' => $date,
        ]);

        return $enrollment;
    }

    /**
     * @param LegacySchoolClass $schoolClass
     *
     * @return Collection
     */
    public function getBySchoolClass($schoolClass)
    {
        return LegacyEnrollment::query()
            ->with([
                'registration' => function ($query) use ($schoolClass) {
                    /** @var Builder $query */
                    $query->where('ano', $schoolClass->year);
                    $query->whereIn('aprovado', [1, 2, 3]);
                    $query->with('student.person');
                }
            ])
            ->where('ref_cod_turma', $schoolClass->id)
            ->where('ativo', 1)
            ->orderBy('sequencial_fechamento')
            ->get();
    }

    /**
     * @param LegacySchoolClass $schoolClass
     *
     * @return Collection
     */
    public function getRegistrationsNotEnrolled($schoolClass)
    {
        return LegacyRegistration::query()
            ->with('student.person', 'lastEnrollment')
            ->where('ref_cod_curso', $schoolClass->course_id)
            ->where('ref_ref_cod_serie', $schoolClass->grade_id)
            ->where('ref_ref_cod_escola', $schoolClass->school_id)
            ->where('ativo', 1)
            ->where('ultima_matricula', 1)
            ->where('ano', $schoolClass->year)
            ->whereIn('aprovado', [1, 2, 3])
            ->whereDoesntHave('enrollments', function (Builder $query) use ($schoolClass) {
                $query->where('ativo', 1);
                $query->whereHas('schoolClass', function (Builder $query) use ($schoolClass) {
                    $query->where('ref_ref_cod_escola', $schoolClass->school_id);
                    $query->where('ativo', 1);
                });
            })
            ->get();
    }
}
