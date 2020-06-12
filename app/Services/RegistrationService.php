<?php

namespace App\Services;

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyTransferRequest;
use App\User;
use App_Model_MatriculaSituacao;
use clsModulesAuditoriaGeral;
use DateTime;
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
     * @param LegacyRegistration $registration
     * @param array $data
     */
    public function updateStatus(LegacyRegistration $registration, $data)
    {
        $status = $data['nova_situacao'];
        $auditoria = new clsModulesAuditoriaGeral('update_registration_status', $registration->getKey());
        $auditoria->usuario_id = $this->user->getKey();
        $auditoria->alteracao(
            ['aprovado' => $registration->aprovado],
            ['aprovado' => $status]
        );

        $registration->aprovado = $status;
        $registration->save();

        $this->checkUpdatedStatusAction($data, $registration);
    }

    /**
     * @param array $data
     * @param LegacyRegistration $registration
     */
    private function checkUpdatedStatusAction($data, LegacyRegistration $registration)
    {
        $newStatus = $data['nova_situacao'];

        if ($newStatus == App_Model_MatriculaSituacao::TRANSFERIDO) {
            $this->markEnrollmentsAsTransferred($registration);
            $this->createTransferRequest(
                $data['transferencia_data'],
                $data['transferencia_tipo'],
                $data['transferencia_observacoes'],
                $registration
            );
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

    /**
     * @param string $date
     * @param integer $type
     * @param string $comments
     * @param LegacyRegistration $registration
     */
    private function createTransferRequest($date, $type, $comments, $registration)
    {
        LegacyTransferRequest::create([
            'ref_cod_transferencia_tipo' => $type,
            'ref_usuario_cad' => $this->user->getKey(),
            'ref_cod_matricula_saida' => $registration->getKey(),
            'observacao' => $comments,
            'data_cadastro' => now(),
            'ativo' => 1,
            'data_transferencia' => DateTime::createFromFormat('d/m/Y', $date),
        ]);
    }

    /**
     * Atualiza a data de entrada de uma matrícula
     *
     * @param LegacyRegistration $registration
     * @param DateTime $date
     */
    public function updateRegistrationDate(LegacyRegistration $registration, DateTime $date)
    {
        $date = $date->format('Y-m-d');
        $auditoria = new clsModulesAuditoriaGeral('update_registration_date', $registration->getKey());
        $auditoria->usuario_id = $this->user->getKey();
        $auditoria->alteracao(
            ['data_matricula' => $registration->data_matricula],
            ['data_matricula' => $date]
        );

        $registration->data_matricula = $date;
        $registration->save();
    }

    /**
     * Atualiza a date de enturmação de todas as enturmações de uma matrícula
     *
     * @param LegacyRegistration $registration
     * @param DateTime $date
     * @param DateTime|null $oldData
     * @param boolean $relocated
     */
    public function updateEnrollmentsDate(LegacyRegistration $registration, DateTime $date, $oldData, $relocated)
    {
        $date = $date->format('Y-m-d');

        foreach ($registration->enrollments as $enrollment) {
            if ($oldData && $enrollment->data_enturmacao->format('Y-m-d') != $oldData->format('Y-m-d')) {
                continue;
            }

            if (!$relocated && $enrollment->remanejado) {
                continue;
            }

            $auditoria = new clsModulesAuditoriaGeral('update_enrollment_date', $enrollment->getKey());
            $auditoria->usuario_id = $this->user->getKey();
            $auditoria->alteracao(
                ['data_enturmacao' => $enrollment->data_enturmacao],
                ['data_enturmacao' => $date]
            );

            $enrollment->data_enturmacao = $date;
            $enrollment->save();
        }
    }
}
