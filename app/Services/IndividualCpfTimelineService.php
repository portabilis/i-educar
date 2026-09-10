<?php

namespace App\Services;

use App\Models\DocumentAbsenceReason;
use App\Models\LegacyIndividual;
use App\Models\Timeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class IndividualCpfTimelineService
{
    public const TYPE = 'individual-cpf-updated';

    /**
     * Registra a alteração do CPF, e nada grava quando o estado não muda
     */
    public function register(int $individualId, array $before, array $after): ?Timeline
    {
        $before = $this->describe($before);
        $after = $this->describe($after);

        if ($before === $after) {
            return null;
        }

        return Timeline::create([
            'model_id' => $individualId,
            'model_type' => LegacyIndividual::class,
            'type' => self::TYPE,
            'payload' => [
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()?->name,
                ],
                'before' => $before,
                'after' => $after,
            ],
        ]);
    }

    /**
     * Histórico completo, do mais recente para o mais antigo
     *
     * @return Collection<int, Timeline>
     */
    public function history(int $individualId): Collection
    {
        return Timeline::query()
            ->where('model_type', LegacyIndividual::class)
            ->where('model_id', $individualId)
            ->where('type', self::TYPE)
            // por id, e não por data: a coluna não guarda fração de segundo
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Traduz o estado do campo para os rótulos exibidos na trilha
     */
    private function describe(array $state): array
    {
        $cpf = $state['cpf'] ?? null;

        if ($cpf) {
            return ['CPF' => int2CPF($cpf)];
        }

        $reasonId = $state['document_absence_reason_id'] ?? null;

        if (!$reasonId) {
            return ['CPF' => null];
        }

        $described = [
            'CPF' => null,
            'Motivo da ausência' => DocumentAbsenceReason::withTrashed()->find($reasonId)?->name,
        ];

        $observation = $state['document_absence_observation'] ?? null;

        if ($observation) {
            $described['Observações'] = $observation;
        }

        return $described;
    }
}
