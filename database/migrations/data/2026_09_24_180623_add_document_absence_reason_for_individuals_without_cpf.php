<?php

use App\Models\DocumentAbsenceReason;
use App\Models\LegacyIndividual;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $reason = DocumentAbsenceReason::withTrashed()->firstOrCreate(
            ['from_system' => true],
            [
                'name' => 'Sem motivo - cadastro antigo',
                'requires_observation' => false,
            ]
        );

        LegacyIndividual::query()
            ->where(fn ($query) => $query->whereNull('cpf')->orWhere('cpf', 0))
            ->whereNull('document_absence_reason_id')
            ->update([
                'document_absence_reason_id' => $reason->getKey(),
            ]);
    }
};
