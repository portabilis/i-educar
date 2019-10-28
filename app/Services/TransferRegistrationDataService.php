<?php

namespace App\Services;

use App\Exceptions\Transfer\StagesAreNotSame;
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

        if (!$this->hasSameStages($this->registration, $transfer->oldRegistration)) {
            throw new StagesAreNotSame();
        }

        $copyAbsenceService = new CopyAbsenceService($this->registration, $transfer->oldRegistration);
        $copyAbsenceService->copyAbsences();

        $copyScoreService = new CopyScoreService($this->registration, $transfer->oldRegistration);
        $copyScoreService->copyScores();

        $copyDescriptiveOpnionService = new CopyDescriptiveOpinionService($this->registration, $transfer->oldRegistration);
        $copyDescriptiveOpnionService->copyDescriptiveOpinions();
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

    public function hasSameStages($newRegistration, $oldRegistration)
    {
        $newRegistrationNumbersOfStages = count($newRegistration->lastEnrollment->schoolClass->stages);
        $oldRegistrationNumbersOfStages = count($oldRegistration->lastEnrollment->schoolClass->stages);

        return $newRegistrationNumbersOfStages == $oldRegistrationNumbersOfStages;
    }

}
