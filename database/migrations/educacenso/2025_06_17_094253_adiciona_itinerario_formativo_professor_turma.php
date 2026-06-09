<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma ADD COLUMN area_itinerario smallint[];');
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma ADD COLUMN leciona_itinerario_tecnico_profissional smallint');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma DROP COLUMN leciona_itinerario_tecnico_profissional;');
        DB::statement('ALTER TABLE IF EXISTS modules.professor_turma DROP COLUMN area_itinerario;');
    }
};
