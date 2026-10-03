<?php

namespace App\Services;

use App\Models\DocumentAbsenceReason;
use App\Models\LegacyIndividual;
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

    public function validate(array $state, ?int $individualId = null): ?array
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

        $savedReasonId = $individualId
            ? LegacyIndividual::query()->whereKey($individualId)->value('document_absence_reason_id')
            : null;

        $reason = DocumentAbsenceReason::query()
            ->when(
                (int) $savedReasonId === $reasonId,
                fn ($query) => $query->withTrashed(),
                fn ($query) => $query->where('from_system', false)
            )
            ->find($reasonId);

        if (!$reason) {
            return ['document_absence_reason_id' => 'O motivo da ausência do CPF informado não existe.'];
        }

        if ($reason->requires_observation && !trim((string) $this->observation($state))) {
            return ['document_absence_observation' => 'Este motivo exige o preenchimento das observações.'];
        }

        return null;
    }

    public function reasonOptions(?int $currentReasonId = null): array
    {
        $currentReasonId = $this->reasonId($currentReasonId);

        return DocumentAbsenceReason::withTrashed()
            ->where(function ($query) use ($currentReasonId) {
                $query->where(function ($query) {
                    $query->whereNull('deleted_at');
                    $query->where('from_system', false);
                });
                $query->orWhere('id', $currentReasonId);
            })
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (DocumentAbsenceReason $reason) => [$reason->getKey() => $this->label($reason)])
            ->prepend('Selecione', '')
            ->toArray();
    }

    public function inactiveReasons(): Collection
    {
        return DocumentAbsenceReason::onlyTrashed()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (DocumentAbsenceReason $reason) => [$reason->getKey() => $this->label($reason)]);
    }

    public function systemReasons(): Collection
    {
        return DocumentAbsenceReason::withTrashed()
            ->where('from_system', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (DocumentAbsenceReason $reason) => [$reason->getKey() => $this->label($reason)]);
    }

    private function label(DocumentAbsenceReason $reason): string
    {
        return $reason->trashed() ? $reason->name . ' (inativo)' : $reason->name;
    }

    public function reasonId(mixed $value): ?int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => self::MAX_REASON_ID]]);

        return $id === false ? null : $id;
    }

    private function observation(array $state): ?string
    {
        $observation = $state['document_absence_observation'] ?? null;

        return is_string($observation) ? $observation : null;
    }

    public function situation(LegacyIndividual $individual): array
    {
        if ($individual->cpf) {
            return ['type' => 'with_cpf', 'cpf' => $individual->cpf];
        }

        $reason = $individual->document_absence_reason_id
            ? DocumentAbsenceReason::withTrashed()->find($individual->document_absence_reason_id)
            : null;

        if (!$reason || $reason->from_system) {
            return ['type' => 'unjustified_absence'];
        }

        $timeline = new IndividualCpfTimelineService;
        $lastChange = $timeline->history($individual->getKey())->first();

        return [
            'type' => 'justified_absence',
            'reason' => $reason->name,
            'user' => $lastChange ? $timeline->userName($lastChange->payload) : null,
            'date' => $lastChange?->created_at->format('d/m/Y'),
        ];
    }

    public function reasonsRequiringObservation(): Collection
    {
        return DocumentAbsenceReason::withTrashed()
            ->where('requires_observation', true)
            ->pluck('id');
    }
}
