<?php

namespace App\Services;

use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Exceptions\Transfer\StagesAreNotSame;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyRegistration;

class RegistrationDataService
{
    /**
     * @var CopyAbsenceService
     */
    private $copyAbsenceService;

    /**
     * @var CopyScoreService
     */
    private $copyScoreService;

    private $message;

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
    public function copy(LegacyRegistration $newRegistration, LegacyRegistration $oldRegistration)
    {
        if (!$this->hasSameStages($newRegistration, $oldRegistration)) {
            throw new StagesAreNotSame();
        }
        $this->copyAbsenceService->copy($newRegistration, $oldRegistration);
        $this->copyDescriptiveOpinionService->copy($newRegistration, $oldRegistration);
        $this->copyScoreService->copy($newRegistration, $oldRegistration);
    }

    public function hasSameStages(
        LegacyRegistration $newRegistration,
        LegacyRegistration $oldRegistration
    ): bool {
        $newRegistrationNumbersOfStages = LegacyAcademicYearStage::query()
            ->whereSchool($newRegistration->ref_ref_cod_escola)
            ->whereYearEq($newRegistration->ano)
            ->count();

        $oldRegistrationNumbersOfStages = count($oldRegistration?->lastEnrollment?->schoolClass?->getStages($oldRegistration?->course?->is_standard_calendar) ?? []);

        return $newRegistrationNumbersOfStages == $oldRegistrationNumbersOfStages;
    }
}
