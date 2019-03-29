<?php

namespace App\Services;

use App\Exceptions\Enrollment\CancellationDateAfterAcademicYearException;
use App\Exceptions\Enrollment\CancellationDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateAfterAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Enrollment\NoVacancyException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyEnrollment;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use SequencialEnturmacao;
use Throwable;

class EnrollmentService
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param LegacyRegistration $registration
     * @param LegacySchoolClass $schoolClass
     * @param DateTime $date
     *
     * @return null
     */
    private function getSequenceSchoolClass(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        DateTime $date
    ) {
        $enrollmentSequence = new SequencialEnturmacao($registration->id, $schoolClass->id, $date->format('Y-m-d'));

        return $enrollmentSequence->ordenaSequencialNovaMatricula();
    }

    /**
     * Retorna a enturmação.
     *
     * @param int $enrollment ID da enturmação
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
     * @param array $ids
     *
     * @return Collection
     */
    public function findAll(array $ids)
    {
        return LegacyEnrollment::query()
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Cancela uma enturmação.
     *
     * @param LegacyEnrollment $enrollment ID da enturmação
     * @param DateTime         $date       Data do cancelamento
     *
     * @return bool
     *
     * @throws PreviousCancellationDateException
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function cancelEnrollment(LegacyEnrollment $enrollment, DateTime $date)
    {
        if ($date->format('Y-m-d') < $enrollment->schoolClass->begin_academic_year->format('Y-m-d')) {
            throw new CancellationDateBeforeAcademicYearException($enrollment->schoolClass, $date);
        }

        if ($date->format('Y-m-d') > $enrollment->schoolClass->end_academic_year->format('Y-m-d')) {
            throw new CancellationDateAfterAcademicYearException($enrollment->schoolClass, $date);
        }

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
     * @param DateTime           $date
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
        DateTime $date
    ) {
        if ($schoolClass->denyEnrollmentsWhenNoVacancy() && empty($schoolClass->vacancies)) {
            throw new NoVacancyException($schoolClass);
        }

        if ($date->format('Y-m-d') < $schoolClass->begin_academic_year->format('Y-m-d')) {
            throw new EnrollDateBeforeAcademicYearException($schoolClass, $date);
        }

        if ($date->format('Y-m-d') > $schoolClass->end_academic_year->format('Y-m-d')) {
            throw new EnrollDateAfterAcademicYearException($schoolClass, $date);
        }

        $existsActiveEnrollment = $registration->enrollments()
            ->where('ativo', 1)
            ->where('ref_cod_turma', $schoolClass->id)
            ->count();

        if ($existsActiveEnrollment) {
            throw new ExistsActiveEnrollmentException($registration);
        }

        if ($registration->lastEnrollment && $registration->lastEnrollment->date_departed->format('Y-m-d') > $date->format('Y-m-d')) {
            throw new PreviousEnrollDateException($date, $registration->lastEnrollment);
        }

        $sequenceInSchoolClass = $this->getSequenceSchoolClass($registration, $schoolClass, $date);

        /** @var LegacyEnrollment $enrollment */
        $enrollment = $registration->enrollments()->create([
            'ref_cod_turma' => $schoolClass->id,
            'sequencial' => $registration->enrollments()->max('sequencial') + 1,
            'sequencial_fechamento' => $sequenceInSchoolClass,
            'ref_usuario_cad' => $this->user->getKey(),
            'data_cadastro' => Carbon::now(),
            'data_enturmacao' => $date,
        ]);

        return $enrollment;
    }
}
