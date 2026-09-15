<?php

namespace App\Services;

use App\Models\DocumentAbsenceReason;
use App\Models\LegacyIndividual;

class IndividualCpfStateService
{
    public function normalize(array $state): array
    {
        if (!$state['possui_cpf']) {
            $state['cpf'] = null;

            return $state;
        }

        $state['document_absence_reason_id'] = null;
        $state['document_absence_observation'] = null;

        return $state;
    }

    public function validate(array $state, ?int $individualId = null): ?array
    {
        if ($state['possui_cpf']) {
            return $state['cpf'] ? null : ['id_federal' => 'É necessário o preenchimento do CPF.'];
        }

        $reasonId = $state['document_absence_reason_id'];

        if (!is_numeric($reasonId)) {
            return ['document_absence_reason_id' => 'É necessário informar o motivo da ausência do CPF.'];
        }

        $savedReasonId = $individualId
            ? LegacyIndividual::query()->whereKey($individualId)->value('document_absence_reason_id')
            : null;

        $reason = DocumentAbsenceReason::query()
            ->when((int) $savedReasonId === (int) $reasonId, fn ($query) => $query->withTrashed())
            ->find($reasonId);

        if (!$reason) {
            return ['document_absence_reason_id' => 'O motivo da ausência do CPF informado não existe.'];
        }

        if ($reason->requires_observation && !trim((string) $state['document_absence_observation'])) {
            return ['document_absence_observation' => 'Este motivo exige o preenchimento das observações.'];
        }

        return null;
    }

    public function reasonOptions(?int $currentReasonId = null): array
    {
        $reasons = DocumentAbsenceReason::query()->orderBy('name')->pluck('name', 'id');

        if ($currentReasonId && !$reasons->has($currentReasonId)) {
            $inactive = DocumentAbsenceReason::withTrashed()->find($currentReasonId);

            if ($inactive) {
                $reasons->put($inactive->id, $inactive->name . ' (inativo)');
            }
        }

        return $reasons->prepend('Selecione', '')->toArray();
    }

    public function reasonsRequiringObservation(): array
    {
        return DocumentAbsenceReason::withTrashed()
            ->where('requires_observation', true)
            ->pluck('id')
            ->all();
    }
}
