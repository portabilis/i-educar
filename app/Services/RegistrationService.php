<?php

namespace App\Services;

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\User;
use App_Model_MatriculaSituacao;
use clsModulesAuditoriaGeral;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RegistrationService
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
     * @param array $ids
     *
     * @return Collection
     */
    public function findAll(array $ids)
    {
        return LegacyRegistration::query()
            ->whereIn('cod_matricula', $ids)
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
            ->whereDoesntHave('enrollments', function ($query) use ($schoolClass) {
                /** @var Builder $query */
                $query->where('ativo', 1);
                $query->whereHas('schoolClass', function ($query) use ($schoolClass) {
                    /** @var Builder $query */
                    $query->where('ref_ref_cod_escola', $schoolClass->school_id);
                    $query->where('ativo', 1);
                });
            })
            ->get();
    }

    /**
     * Atualiza a situação de uma matrícula
     *
     * @param $registration
     * @param $status
     */
    public function updateStatus(LegacyRegistration $registration, $status)
    {
        $auditoria = new clsModulesAuditoriaGeral('update_registration_status', $this->user->getKey());
        $auditoria->alteracao(
            ['aprovado' => $registration->aprovado],
            ['aprovado' => $status]
        );

        $registration->aprovado = $status;
        $registration->save();

        $this->checkUpdatedStatusAction($status, $registration);
    }

    /**
     * @param $newStatus
     * @param LegacyRegistration $registration
     */
    private function checkUpdatedStatusAction($newStatus, LegacyRegistration $registration)
    {
        if ($newStatus == App_Model_MatriculaSituacao::TRANSFERIDO) {
            $this->markEnrollmentsAsTransferred($registration);
        }

        if ($newStatus == App_Model_MatriculaSituacao::RECLASSIFICADO) {
            $this->markEnrollmentsAsReclassified($registration);
        }

        if ($newStatus == App_Model_MatriculaSituacao::ABANDONO) {
            $this->markEnrollmentsAsAbandoned($registration);
        }

        if ($newStatus == App_Model_MatriculaSituacao::FALECIDO) {
            $this->markEnrollmentsAsDeceased($registration);
        }
    }

    private function markEnrollmentsAsTransferred(LegacyRegistration $registration)
    {
        foreach ($this->getActiveEnrollments($registration) as $enrollment) {
            app(EnrollmentService::class)->markAsTransferred($enrollment);
        }
    }

    private function markEnrollmentsAsReclassified(LegacyRegistration $registration)
    {
        foreach ($this->getActiveEnrollments($registration) as $enrollment) {
            app(EnrollmentService::class)->markAsReclassified($enrollment);
        }
    }

    private function markEnrollmentsAsAbandoned(LegacyRegistration $registration)
    {
        foreach ($this->getActiveEnrollments($registration) as $enrollment) {
            app(EnrollmentService::class)->markAsAbandoned($enrollment);
        }
    }

    private function markEnrollmentsAsDeceased(LegacyRegistration $registration)
    {
        foreach ($this->getActiveEnrollments($registration) as $enrollment) {
            app(EnrollmentService::class)->markAsDeceased($enrollment);
        }
    }

    /**
     * @param LegacyRegistration $registration
     * @return LegacyEnrollment
     */
    private function getActiveEnrollments(LegacyRegistration $registration)
    {
        return $registration->activeEnrollments;
    }
}
