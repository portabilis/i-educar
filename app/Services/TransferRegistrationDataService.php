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

    /**
     * @param CopyAbsenceService            $copyAbsenceService
     * @param CopyScoreService              $copyScoreService
     * @param CopyDescriptiveOpinionService $copyDescriptiveOpinionService
     */
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
     * @param LegacyRegistration $registration
     *
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
     * @param LegacyRegistration $newRegistration
     * @param LegacyRegistration $oldRegistration
     *
     * @return bool
     */
    public function hasSameStages(
        LegacyRegistration $newRegistration,
        LegacyRegistration $oldRegistration
    ) {
        $newRegistrationNumbersOfStages = count($newRegistration->lastEnrollment->schoolClass->stages);
        $oldRegistrationNumbersOfStages = count($oldRegistration->lastEnrollment->schoolClass->stages);

        return $newRegistrationNumbersOfStages == $oldRegistrationNumbersOfStages;
    }

    /**
     * @param LegacyRegistration $registration
     *
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
