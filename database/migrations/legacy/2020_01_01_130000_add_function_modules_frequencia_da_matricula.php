<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionModulesFrequenciaDaMatricula extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            file_get_contents(database_path('sqls/functions/modules.frequencia_da_matricula.sql'))
        );
    }

    public function down(): void
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS modules.frequencia_da_matricula(p_matricula_id integer);'
        );
    }
}
