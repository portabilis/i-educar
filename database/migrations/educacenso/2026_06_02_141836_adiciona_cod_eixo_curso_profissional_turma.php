<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma ADD COLUMN cod_eixo_curso_profissional smallint;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE IF EXISTS pmieducar.turma DROP COLUMN cod_eixo_curso_profissional;');
    }
};
