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

    public function history(int $individualId): Collection
    {
        return Timeline::query()
            ->where('model_type', LegacyIndividual::class)
            ->where('model_id', $individualId)
            ->where('type', self::TYPE)
            ->orderByDesc('id')
            ->get();
    }

    public function summary(array $payload): string
    {
        $after = $payload['after'] ?? [];

        if (!empty($after['CPF'])) {
            return 'Informou o CPF ' . $after['CPF'];
        }

        if (!empty($after['Motivo da ausência'])) {
            return 'Marcou "Não possui" com o motivo ' . $after['Motivo da ausência'];
        }

        return 'Removeu o CPF';
    }

    public function userName(array $payload): string
    {
        return $payload['user']['name'] ?? 'Usuário não identificado';
    }

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
