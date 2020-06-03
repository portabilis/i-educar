<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioGetMaeAluno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.get_mae_aluno.sql')
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            'DROP FUNCTION relatorio.get_mae_aluno(integer);'
        );
    }
}
