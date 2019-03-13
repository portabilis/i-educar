<?php

namespace App\Services;

use DateTime;
use Throwable;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @param int $registration ID da matrícula
     * @param int $schoolClass  ID da turma
     * @param int $sequence     Número do sequencial de enturmação
     *
     * @return LegacyEnrollment
     *
     * @throws ModelNotFoundException
     */
    public function find($registration, $schoolClass, $sequence)
    {
        /** @var LegacyEnrollment $enrollment */
        $enrollment = LegacyEnrollment::query()
            ->where('ref_cod_matricula', $registration)
            ->where('ref_cod_turma', $schoolClass)
            ->where('sequencial', $sequence)
            ->firstOrFail();

        return $enrollment;
    }

    /**
     * Retorna o maior sequencial de enturmação ativo para a matrícula na
     * turma. Como fallback retorna 1.
     *
     * @param int $registration ID da matrícula
     * @param int $schoolClass  ID da turma
     *
     * @return int
     */
    public function getMaxActiveSequence($registration, $schoolClass)
    {
        $enrollment = LegacyEnrollment::query()
            ->where('ativo', 1)
            ->where('ref_cod_matricula', $registration)
            ->where('ref_cod_turma', $schoolClass)
            ->max('sequencial');

        return $enrollment ?? 1;
    }

    /**
     * Cancela uma enturmação.
     *
     * @param int      $registration ID da matrícula
     * @param int      $schoolClass  ID da turma
     * @param DateTime $date         Data do cancelamento
     *
     * @return bool
     *
     * @throws PreviousCancellationDateException
     * @throws ModelNotFoundException
     * @throws Throwable
     */
    public function cancelEnrollment($registration, $schoolClass, $date)
    {
        $sequence = $this->getMaxActiveSequence($registration, $schoolClass);
        $enrollment = $this->find($registration, $schoolClass, $sequence);

        if ($date < $enrollment->date) {
            throw new PreviousCancellationDateException($enrollment, $date);
        }

        $enrollment->ref_usuario_exc = $this->user->getKey();
        $enrollment->data_exclusao = $date;
        $enrollment->ativo = 0;

        return $enrollment->saveOrFail();
    }
}
