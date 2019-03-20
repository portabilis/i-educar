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
}
