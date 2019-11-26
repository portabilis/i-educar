<?php

namespace App\Services;

use App\Exceptions\Enrollment\CancellationDateAfterAcademicYearException;
use App\Exceptions\Enrollment\CancellationDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateAfterAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateBeforeAcademicYearException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentSameTimeException;
use App\Exceptions\Enrollment\NoVacancyException;
use App\Exceptions\Enrollment\PreviousEnrollCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollRegistrationDateException;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyEnrollment;
use App\Services\SchoolClass\AvailableTimeService;
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
     * @param LegacySchoolClass  $schoolClass
     * @param DateTime           $date
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
     * @return AvailableTimeService
     */
    private function getAvailableTimeService()
    {
        return new AvailableTimeService();
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
     * Retorna se matrícula está enturmada na turma.
     *
     * @param LegacySchoolClass  $schoolClass
     * @param LegacyRegistration $registration
     *
     * @return bool
     */
    public function isEnrolled($schoolClass, $registration)
    {
        return LegacyEnrollment::where('ref_cod_matricula', $registration->id)
            ->where('ref_cod_turma', $schoolClass->id)
            ->active()
            ->exists();
    }

    /**
     * Retorna as enturmações da matrícula em outras turmas.
     *
     * @param LegacySchoolClass  $schoolClass
     * @param LegacyRegistration $registration
     *
     * @return Collection
     */
    public function anotherClassroomEnrollments($schoolClass, $registration)
    {
        return LegacyEnrollment::where('ref_cod_matricula', $registration->id)
            ->where('ref_cod_turma', '<>', $schoolClass->id)
            ->active()
            ->with('schoolClass')
            ->get();
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
        $schoolClass = $enrollment->schoolClass;

        if ($date->format('Y-m-d') < $enrollment->schoolClass->begin_academic_year->format('Y-m-d')) {
            if (!$schoolClass->school->institution->allowRegistrationOutAcademicYear) {
                throw new CancellationDateBeforeAcademicYearException($enrollment->schoolClass, $date);
            }
        }

        if ($date->format('Y-m-d') > $enrollment->schoolClass->end_academic_year->format('Y-m-d')) {
            throw new CancellationDateAfterAcademicYearException($enrollment->schoolClass, $date);
        }

        if ($date < $enrollment->date) {
            throw new PreviousCancellationDateException($enrollment, $date);
        }

        if ($date < $enrollment->registration->data_matricula) {
            throw new PreviousEnrollCancellationDateException($enrollment->registration, $date);
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
     * @throws Throwable
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
            if (!$schoolClass->school->institution->allowRegistrationOutAcademicYear) {
                throw new EnrollDateBeforeAcademicYearException($schoolClass, $date);
            }
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

        if (
            $registration->lastEnrollment
            && $registration->lastEnrollment->date_departed
            && $registration->lastEnrollment->date_departed->format('Y-m-d') > $date->format('Y-m-d')
        ) {
            throw new PreviousEnrollDateException($date, $registration->lastEnrollment);
        }

        if ($registration->data_matricula > $date) {
            throw new PreviousEnrollRegistrationDateException($date, $registration);
        }

        $isMandatoryCensoFields = $schoolClass->school->institution->isMandatoryCensoFields();

        if ($isMandatoryCensoFields && !$this->getAvailableTimeService()->isAvailable($registration->ref_cod_aluno, $schoolClass->id)) {
            throw new ExistsActiveEnrollmentSameTimeException($registration);
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

    /**
     * Atualiza o campo transferido na enturmação para TRUE
     *
     * @param LegacyEnrollment $enrollment
     * @throws Throwable
     */
    public function markAsTransferred(LegacyEnrollment $enrollment)
    {
        $enrollment->remanejado = true;
        $enrollment->saveOrFail();
    }

    /**
     * Atualiza o campo remanejado na enturmação para TRUE
     *
     * @param LegacyEnrollment $enrollment
     * @throws Throwable
     */
    public function markAsRelocated(LegacyEnrollment $enrollment)
    {
        $enrollment->remanejado = true;
        $enrollment->saveOrFail();
    }

    /**
     * Atualiza o campo reclassificado na enturmação para TRUE
     *
     * @param LegacyEnrollment $enrollment
     * @throws Throwable
     */
    public function markAsReclassified(LegacyEnrollment $enrollment)
    {
        $enrollment->reclassificado = true;
        $enrollment->saveOrFail();
    }

    /**
     * Atualiza o campo abandono na enturmação para TRUE
     *
     * @param LegacyEnrollment $enrollment
     * @throws Throwable
     */
    public function markAsAbandoned(LegacyEnrollment $enrollment)
    {
        $enrollment->abandono = true;
        $enrollment->saveOrFail();
    }

    /**
     * Atualiza o campo falecido na enturmação para TRUE
     *
     * @param LegacyEnrollment $enrollment
     * @throws Throwable
     */
    public function markAsDeceased(LegacyEnrollment $enrollment)
    {
        $enrollment->falecido = true;
        $enrollment->saveOrFail();
    }

    /**
     * Verifica se a matrícula tem enturmação anterior, com data de saída posterior a data base,
     * ou data base vazia
     *
     * @param LegacyRegistration $registration
     * @return LegacyEnrollment|void
     */
    public function getPreviousEnrollmentAccordingToRelocationDate(LegacyRegistration $registration)
    {
        $previousEnrollment = $registration->lastEnrollment;

        if (!$previousEnrollment) {
            return;
        }

        $dateDeparted = $previousEnrollment->date_departed;

        $relocationDate = $previousEnrollment->schoolClass->school->institution->relocation_date;

        if (!$relocationDate || $relocationDate < $dateDeparted) {
           return $previousEnrollment;
        }
    }

    /**
     * Reordena os sequenciais das enturmações de uma matrícula.
     *
     * @param LegacyRegistration $registration
     *
     * @return bool
     */
    public function reorder(LegacyRegistration $registration)
    {
        $registration->enrollments()->orderBy('sequencial')->get()->values()->each(function (LegacyEnrollment $enrollment, $index) {
            $enrollment->sequencial = $index + 1;
            $enrollment->save();
        });

        return true;
    }
}
