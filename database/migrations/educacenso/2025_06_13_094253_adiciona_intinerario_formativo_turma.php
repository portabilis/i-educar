<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma ADD COLUMN area_itinerario smallint[];');
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma ADD COLUMN tipo_curso_intinerario smallint;');
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma ADD COLUMN cod_curso_profissional_intinerario smallint;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma DROP COLUMN area_itinerario;');
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma DROP COLUMN tipo_curso_intinerario;');
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma DROP COLUMN cod_curso_profissional_intinerario;');
    }
};
