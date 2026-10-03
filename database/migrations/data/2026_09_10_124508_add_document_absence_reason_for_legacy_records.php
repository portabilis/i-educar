<?php

use App\Models\DocumentAbsenceReason;
use App\Models\LegacyIndividual;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $reason = DocumentAbsenceReason::query()->firstOrCreate(
            ['name' => 'Sem motivo - cadastro antigo'],
            [
                'requires_observation' => false,
                'from_system' => true,
            ]
        );

        LegacyIndividual::query()
            ->whereNull('cpf')
            ->whereNull('document_absence_reason_id')
            ->update([
                'possui_cpf' => false,
                'document_absence_reason_id' => $reason->getKey(),
            ]);
    }

    public function down(): void
    {
        $reason = DocumentAbsenceReason::withTrashed()
            ->where('name', 'Sem motivo - cadastro antigo')
            ->where('from_system', true)
            ->first();

        if (!$reason) {
            return;
        }

        LegacyIndividual::query()
            ->where('document_absence_reason_id', $reason->getKey())
            ->update([
                'possui_cpf' => true,
                'document_absence_reason_id' => null,
                'document_absence_observation' => null,
            ]);

        $reason->forceDelete();
    }
};
