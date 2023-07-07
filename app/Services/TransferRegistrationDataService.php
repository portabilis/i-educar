<?php

namespace App\Services;

use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Exceptions\Transfer\StagesAreNotSame;
use App\Models\LegacyRegistration;
use App\Models\LegacyTransferRequest;

class TransferRegistrationDataService
{
    /**
     * @var CopyAbsenceService
     */
    private $copyAbsenceService;

    /**
     * @var CopyScoreService
     */
    private $copyScoreService;

    /**
     * @var CopyDescriptiveOpinionService
     */
    private $copyDescriptiveOpinionService;

    public function __construct(
        CopyAbsenceService $copyAbsenceService,
        CopyScoreService $copyScoreService,
        CopyDescriptiveOpinionService $copyDescriptiveOpinionService
    ) {
        $this->copyAbsenceService = $copyAbsenceService;
        $this->copyScoreService = $copyScoreService;
        $this->copyDescriptiveOpinionService = $copyDescriptiveOpinionService;
    }

    /**
     * @throws StagesAreNotSame
     * @throws MissingDescriptiveOpinionType
     */
    public function transferData(LegacyRegistration $registration)
    {
        $transfer = $this->getTransfer($registration);

        if (!$transfer) {
            return;
        }

        if (!$this->hasSameStages($registration, $transfer->oldRegistration)) {
            throw new StagesAreNotSame();
        }

        $this->copyAbsenceService->copy($registration, $transfer->oldRegistration);
        $this->copyScoreService->copy($registration, $transfer->oldRegistration);
        $this->copyDescriptiveOpinionService->copy($registration, $transfer->oldRegistration);
    }

    /**
     * @return bool
     */
    public function hasSameStages(
        LegacyRegistration $newRegistration,
        LegacyRegistration $oldRegistration
    ) {
        $newRegistrationNumbersOfStages = count($newRegistration?->lastEnrollment?->schoolClass?->getStages($newRegistration?->course?->is_standard_calendar) ?? []);
        $oldRegistrationNumbersOfStages = count($oldRegistration?->lastEnrollment?->schoolClass?->getStages($oldRegistration?->course?->is_standard_calendar) ?? []);

        return $newRegistrationNumbersOfStages == $oldRegistrationNumbersOfStages;
    }

    /**
     * @return LegacyTransferRequest
     */
    public function getTransfer(LegacyRegistration $registration)
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
