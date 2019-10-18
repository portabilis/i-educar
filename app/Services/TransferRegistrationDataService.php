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
        $transfers = $this->getTransfers();

        if (!$transfers) {
            return;
        }

        foreach ($transfers as $transfer) {
            $this->compatibleEvaluationRule($this->registration, $transfer->oldRegistration);
        }
    }

    public function getTransfers()
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

        $tranfers = LegacyTransferRequest::query()
            ->active()
            ->unattended()
            ->whereIn('ref_cod_matricula_saida', $registrationsId)
            ->get();

        return $tranfers;
    }

    public function compatibleEvaluationRule($newRegistration, $oldRegistration)
    {
        // dd($oldRegistration);
        $newRegra = new RegistrationEvaluationRuleService($newRegistration);
        $oldRegra = new RegistrationEvaluationRuleService($oldRegistration);

        dd($newRegra->getEvaluationRule());
        // tipo de nota
        // tipo de falta
        // tipo de parecer
       
    //    if ($this->escolaUsaRegraDiferenciada($newRegistration) == $this->escolaUsaRegraDiferenciada($oldRegistration)) {
    //        return true;
    //    }

    }

    public function escolaUsaRegraDiferenciada($registration)
    {
        return $registration->school->utiliza_regra_diferenciada;
    }

    public function registrationEvaluationRule($registration)
    {

    }

}
