<?php

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use Throwable;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyEnrollment;
use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
`     * @param int $enrollment ID da enturmação
     *
     * @return LegacyEnrollment
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
     * @param int $schoolClass
     * @param int $academicYear
     *
     * @return Collection
     */
    public function getBySchoolClass($schoolClass, $academicYear)
    {
        return LegacyEnrollment::query()
            ->with([
                'registration' => function ($query) use ($academicYear) {
                    /** @var Builder $query */
                    $query->where('ano', $academicYear);
                    $query->whereIn('aprovado', [1, 2, 3]);
                    $query->with('student.person');
                }
            ])
            ->where('ref_cod_turma', $schoolClass)
            ->where('ativo', 1)
            ->orderBy('sequencial_fechamento')
            ->get();
    }

    public function getRegistrationsNotEnrolled($schoolClass)
    {
        $schoolClass = LegacySchoolClass::findOrFail($schoolClass);

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
