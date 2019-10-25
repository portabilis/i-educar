<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App\Models\LegacyTransferRequest;

class TransferRegistrationDataService
{

    public function __construct(LegacyRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function transferData()
    {
        $transfer = $this->getTransfer();

        if (!$transfer) {
            return;
        }

        if (!$this->hasSameSteps($this->registration, $transfer->oldRegistration)) {
            // Construir feedback para o usuário
            return;
        }

        $service = new RegistrationEvaluationRuleService($this->registration);
        $newRegra = $service->getEvaluationRule();
        $service = new RegistrationEvaluationRuleService($transfer->oldRegistration);
        $oldRegra = $service->getEvaluationRule();
        
    }

    public function getTransfer()
    {
        $levelId = $this->registration->ref_ref_cod_serie;
        $year = $this->registration->ano;
        $registrationsId = $this->registration->student
            ->registrations()
            ->active()
            ->where('ref_ref_cod_serie', $levelId)
            ->where('ano', $year)
            ->pluck('cod_matricula')
            ->all();

        return LegacyTransferRequest::query()
            ->active()
            ->unattended()
            ->whereIn('ref_cod_matricula_saida', $registrationsId)
            ->orderBy('data_transferencia', 'desc')
            ->first();
    }

    public function hasSameSteps($newRegistration, $oldRegistration)
    {
        $newRegistrationNumbersOfStages = count($newRegistration->lastEnrollment->schoolClass->stages);
        $oldRegistrationNumbersOfStages = count($oldRegistration->lastEnrollment->schoolClass->stages);

        return $newRegistrationNumbersOfStages == $oldRegistrationNumbersOfStages;
    }

}
