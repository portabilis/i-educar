<?php

namespace App\Services;

use App\Models\LegacyRegistration;

class CopyAbsenceService
{
    private $newRegistration;
    private $oldRegistration;
    private $newEvaluation;
    private $oldEvaluation;
    private $absenceType;

    public function __construct(LegacyRegistration $newRegistration, LegacyRegistration $oldRegistration)
    {
        $this->newRegistration = $newRegistration;
        $this->oldRegistration = $oldRegistration;

        $service = new RegistrationEvaluationRuleService($this->newRegistration);
        $this->newEvaluation = $service->getEvaluationRule();

        $service = new RegistrationEvaluationRuleService($this->oldRegistration);
        $this->oldEvaluation = $service->getEvaluationRule();
    }

    public function copyAbsences()
    {
        if (!$this->compatibleAbsenceType()) {
            return;
        }

        
    }

    public function compatibleAbsenceType()
    {
        return $this->newEvaluation->tipo_presenca == $this->oldEvaluation->tipo_presenca;
    }

}
