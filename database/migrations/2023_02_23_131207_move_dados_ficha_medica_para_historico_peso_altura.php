<?php

use App\Models\LegacyStudentHistoricalHeightWeight;
use App\Models\LegacyStudentMedicalRecord;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up()
    {
        $records = LegacyStudentMedicalRecord::query()
            ->whereNotNull('altura')
            ->orWhereNotNull('peso')
            ->get();

        $records->each(function (
            LegacyStudentMedicalRecord $record
        ) {
            if ($record->altura != '' && $record->peso != '' && $record->student()->exists()) {
                LegacyStudentHistoricalHeightWeight::create([
                    'ref_cod_aluno' => $record->ref_cod_aluno,
                    'data_historico' => $record->student->updated_at ?? now(),
                    'altura' => $record->altura,
                    'peso' => $record->peso,
                ]);
            }
        });
    }
};
