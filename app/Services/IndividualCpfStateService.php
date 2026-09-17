<?php

namespace App\Services;

use App\Models\DocumentAbsenceReason;
use Illuminate\Support\Collection;

class IndividualCpfStateService
{
    private const MAX_REASON_ID = 32767;

    public function normalize(array $state): array
    {
        if (!$state['possui_cpf']) {
            $state['cpf'] = null;
            $state['document_absence_observation'] = $this->observation($state);

            return $state;
        }

        $state['document_absence_reason_id'] = null;
        $state['document_absence_observation'] = null;

        return $state;
    }

    public function validate(array $state): ?array
    {
        if ($state['possui_cpf']) {
            return $state['cpf'] ? null : ['id_federal' => 'É necessário o preenchimento do CPF.'];
        }

        $reasonId = $state['document_absence_reason_id'];

        if (!is_numeric($reasonId)) {
            return ['document_absence_reason_id' => 'É necessário informar o motivo da ausência do CPF.'];
        }

        $reasonId = $this->reasonId($reasonId);

        if ($reasonId === null) {
            return ['document_absence_reason_id' => 'O motivo da ausência do CPF informado não existe.'];
        }

        $reason = DocumentAbsenceReason::query()->find($reasonId);

        if (!$reason) {
            return ['document_absence_reason_id' => 'O motivo da ausência do CPF informado não existe.'];
        }

        if ($reason->requires_observation && !trim((string) $this->observation($state))) {
            return ['document_absence_observation' => 'Este motivo exige o preenchimento das observações.'];
        }

        return null;
    }

    public function reasonOptions(): array
    {
        return DocumentAbsenceReason::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('Selecione', '')
            ->toArray();
    }

    private function reasonId(mixed $value): ?int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => self::MAX_REASON_ID]]);

        return $id === false ? null : $id;
    }

    private function observation(array $state): ?string
    {
        $observation = $state['document_absence_observation'] ?? null;

        return is_string($observation) ? $observation : null;
    }

    public function reasonsRequiringObservation(): Collection
    {
        return DocumentAbsenceReason::query()
            ->where('requires_observation', true)
            ->pluck('id');
    }
}
