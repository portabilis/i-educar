<?php

namespace App\Services\Exemption;

use App\Models\LegacyRegistration;

class BatchExemptionService
{
    /**
     * @var ExemptionService
     */
    private $exemptionService;

    /**
     * @var array
     */
    private $registrations;

    /**
     * BatchExemptionService constructor.
     *
     * @param ExemptionService $exemptionService
     */
    public function __construct(ExemptionService $exemptionService)
    {
        $this->exemptionService = $exemptionService;
    }

    public function addRegistration(
        LegacyRegistration $registration,
        $disciplineArray,
        $exemptionTypeId,
        $description,
        $stages
    ) {
        $this->registrations[] = [
            'registration' => $registration,
            'disciplineArray' => $disciplineArray,
            'exemptionTypeId' => $exemptionTypeId,
            'description' => $description,
            'stages' => $stages,
        ];
    }

    public function handle()
    {
        foreach ($this->registrations as $registration) {
            $this->exemptionService->createExemptionByDisciplineArray(
                $registration['registration'],
                $registration['disciplineArray'],
                $registration['exemptionTypeId'],
                $registration['description'],
                $registration['stages'],
            );
        }
    }
}
