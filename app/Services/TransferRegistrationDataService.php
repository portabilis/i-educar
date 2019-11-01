<?php

namespace App\Services;

use App\Exceptions\Transfer\StagesAreNotSame;
use App\Models\LegacyRegistration;
use App\Models\LegacyTransferRequest;

class TransferRegistrationDataService
{
    public function transferData(LegacyRegistration $registration)
    {
        $transfer = self::getTransfer($registration);

        if (!$transfer) {
            return;
        }

        if (!$this->hasSameStages($registration, $transfer->oldRegistration)) {
            throw new StagesAreNotSame();
        }

        $copyAbsenceService = new CopyAbsenceService($registration, $transfer->oldRegistration);
        $copyAbsenceService->copyAbsences();

        $copyScoreService = new CopyScoreService($registration, $transfer->oldRegistration);
        $copyScoreService->copyScores();

        $copyDescriptiveOpnionService = new CopyDescriptiveOpinionService($registration, $transfer->oldRegistration);
        $copyDescriptiveOpnionService->copyDescriptiveOpinions();
    }

    public function hasSameStages($newRegistration, $oldRegistration)
    {
        $newRegistrationNumbersOfStages = count($newRegistration->lastEnrollment->schoolClass->stages);
        $oldRegistrationNumbersOfStages = count($oldRegistration->lastEnrollment->schoolClass->stages);

        return $newRegistrationNumbersOfStages == $oldRegistrationNumbersOfStages;
    }

    public static function getTransfer(LegacyRegistration $registration)
    {
        $levelId = $registration->ref_ref_cod_serie;
        $year = $registration->ano;
        $registrationsId = $registration->student
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

}
