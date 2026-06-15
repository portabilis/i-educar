<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioGetTotalFaltas extends Migration
{
    public function up(): void
    {
        $this->down();

        DB::unprepared(
            file_get_contents(database_path('sqls/functions/relatorio.get_total_faltas.sql'))
        );
    }

    public function down(): void
    {
        DB::unprepared(
            'DROP FUNCTION IF EXISTS relatorio.get_total_faltas(matricula_i integer);'
        );
    }
}
