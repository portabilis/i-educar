<?php

namespace App\Services\Exemption;

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyRegistration;
use App\Rules\CanCreateActiveSearchExemption;
use App\User;
use Exception;

class ActiveSearchExemptionService
{
    /**
     * @var User
     */
    private $user;

    private $exemptionService;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->exemptionService = new ExemptionService($this->user);
    }

    public function createExemptionByDisciplineArray(
        LegacyRegistration $registration,
        $disciplineArray,
        $exemptionTypeId,
        $description,
        $stages,
        $startDate = null,
        $endDate = null,
        $activeSearchResult = null
    ) {
        validator(
            ['exeption' =>
                [
                    'registration' => $registration,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'stages' => $stages,
                    'exemption_type_id' => $exemptionTypeId
                ]
            ],
            ['exeption' => new CanCreateActiveSearchExemption()]
        )->validate();

        foreach ($disciplineArray as $discipline) {
            $this->createExemption($registration, $discipline, $exemptionTypeId, $description, $stages, $startDate, $endDate, $activeSearchResult);
        }
    }

    public function createExemption(
        LegacyRegistration $registration,
        $disciplineId,
        $exemptionTypeId,
        $description,
        $stages,
        $startDate = null,
        $endDate = null,
        $activeSearchResult = null
    ){
        $legacyDisciplineExemption = $this->handleExemptionObject(
            $registration,
            $disciplineId,
            $exemptionTypeId,
            $description,
            $startDate,
            $endDate,
            $activeSearchResult
        );

        if (!$legacyDisciplineExemption->save()) {
            throw new Exception();
        }

        $this->cadastraEtapasDaDispensa($legacyDisciplineExemption, $stages);
    }

    private function handleExemptionObject(
        LegacyRegistration $registration,
        $disciplineId,
        $exemptionTypeId,
        $description,
        $startDate = null,
        $endDate = null,
        $activeSearchResult = null
    ){
        return (new LegacyDisciplineExemption())->fill(
            [
                'ref_cod_matricula' => $registration->getKey(),
                'ref_cod_disciplina' => $disciplineId,
                'ref_cod_escola' => $registration->ref_ref_cod_escola,
                'ref_cod_serie' => $registration->ref_ref_cod_serie,
                'ref_usuario_exc' => $this->user->getKey(),
                'ref_usuario_cad' => $this->user->getKey(),
                'ref_cod_tipo_dispensa' => $exemptionTypeId,
                'data_cadastro' => $startDate,
                'data_exclusao' => null,
                'ativo' => 1,
                'observacao' => $description,
                'data_fim' => empty($endDate) ? null : $endDate,
                'resultado_busca_ativa' => $activeSearchResult
            ]
        );
    }

    public function runsPromotion(LegacyRegistration $registration, $stages)
    {
        $this->exemptionService->runsPromotion($registration, $stages);
    }

    public function cadastraEtapasDaDispensa(LegacyDisciplineExemption $exemption, $stages)
    {
        $this->exemptionService->cadastraEtapasDaDispensa($exemption, $stages);
    }
}
