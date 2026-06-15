<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionModulesFrequenciaPorComponente extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            file_get_contents(database_path('sqls/functions/modules.frequencia_por_componente.sql'))
        );
    }

    public function down(): void
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.frequencia_por_componente(cod_matricula_id integer, cod_disciplina_id integer, cod_turma_id integer);'
        );
    }
}
