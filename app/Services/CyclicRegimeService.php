<?php

namespace App\Services;

use App\Models\LegacyGrade;
use App\Models\LegacyRegistration;
use App_Model_MatriculaSituacao;
use Illuminate\Support\Facades\Cache;

class CyclicRegimeService
{
    /**
     * Retorna todas as matriculas de um ciclo a partir de uma matricula
     *
     * @param int $registration
     *
     * @return LegacyRegistration[]
     */
    public function getAllRegistrationsOfCycle($registration)
    {
        return Cache::store('array')->remember("getAllRegistrationsOfCycle:{$registration}", now()->addMinute(), function () use ($registration) {
            /** @var LegacyRegistration $registration */
            $registration = LegacyRegistration::find($registration);

            $grades = $this->getAllGradesOfCycleByRegistration($registration);

            $registrations = [];
            foreach ($grades as $grade) {
                $result = LegacyRegistration::where('ref_ref_cod_serie', $grade->getKey())
                    ->where('ref_cod_aluno', $registration->ref_cod_aluno)
                    ->whereIn('aprovado', [App_Model_MatriculaSituacao::EM_ANDAMENTO, App_Model_MatriculaSituacao::APROVADO])
                    ->active()
                    ->get()
                    ->first();

                if (empty($result)) {
                    continue;
                }

                $registrations[] = $result;
            }

            return $registrations;
        });
    }

    /**
     * @param LegacyRegistration $registration
     *
     * @return LegacyGrade[]
     */
    public function getAllGradesOfCycleByRegistration($registration)
    {
        return $registration->course->grades()->where('ativo', 1)->orderBy('etapa_curso')->get();
    }
}
