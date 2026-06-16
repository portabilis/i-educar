<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pmieducar.turma ALTER COLUMN tipo_atendimento TYPE integer[] USING CASE WHEN tipo_atendimento IS NOT NULL THEN ARRAY[tipo_atendimento]::integer[] ELSE ARRAY[]::integer[] END;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pmieducar.turma ALTER COLUMN tipo_atendimento TYPE smallint USING CASE WHEN tipo_atendimento IS NOT NULL AND array_length(tipo_atendimento, 1) > 0 THEN (tipo_atendimento[1])::smallint ELSE NULL END;');
    }
};
