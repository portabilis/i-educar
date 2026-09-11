<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use App\Models\RegistrationStatus;
use Illuminate\Support\Facades\Cache;

class DiarioControllerService
{
    public function findMatricula(int $turmaId, int $alunoId): ?LegacyRegistration
    {
        return Cache::remember('matricula_id_' . $turmaId . '_' . $alunoId, now()->addMinutes(5), function () use ($turmaId, $alunoId) {
            return LegacyRegistration::query()
                ->active()
                ->whereHas('enrollments', function ($q) use ($turmaId) {
                    $q->where('ref_cod_turma', $turmaId);
                    $q->where(function ($q) {
                        $q->where('ativo', 1);
                        $q->orWhere('transferido', true);
                    });
                })
                ->whereStudent($alunoId)
                ->whereIn('aprovado', [
                    RegistrationStatus::APPROVED,
                    RegistrationStatus::REPROVED,
                    RegistrationStatus::ONGOING,
                    RegistrationStatus::TRANSFERRED,
                    RegistrationStatus::APPROVED_BY_BOARD,
                    RegistrationStatus::APPROVED_WITH_DEPENDENCY,
                    RegistrationStatus::REPROVED_BY_ABSENCE,
                ])
                ->orderBy('aprovado')
                ->first([
                    'cod_matricula',
                    'ref_ref_cod_serie',
                    'ref_ref_cod_escola',
                    'ano',
                ]);
        });
    }

    public function getRegraAvaliacaoPorMatricula(int $matriculaId)
    {
        return \App_Model_IedFinder::getRegraAvaliacaoPorMatricula($matriculaId);
    }

    public function getServiceBoletim(int $turmaId, int $alunoId, ?LegacyRegistration $registration): \Avaliacao_Service_Boletim
    {
        if (is_null($registration)) {
            $registration = $this->findMatricula($turmaId, $alunoId);
        }

        $params = [
            'matricula' => $registration->getKey(),
        ];

        $boletim = new \Avaliacao_Service_Boletim($params);

        if (is_null($boletim)) {
            throw new \CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matrícula {$registration->getKey()}.");
        }

        return $boletim;
    }
}
