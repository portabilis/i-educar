<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->down();

        DB::unprepared(
            file_get_contents(database_path('sqls/functions/modules.hora_falta_por_componente.sql'))
        );
    }

    public function down(): void
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.hora_falta_por_componente(cod_matricula_id integer, cod_disciplina_id integer);'
        );
    }
};
