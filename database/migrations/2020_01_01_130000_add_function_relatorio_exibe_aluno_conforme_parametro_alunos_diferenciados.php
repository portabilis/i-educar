<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioExibeAlunoConformeParametroAlunosDiferenciados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.exibe_aluno_conforme_parametro_alunos_diferenciados.sql')
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
            'DROP FUNCTION relatorio.exibe_aluno_conforme_parametro_alunos_diferenciados(codigo_aluno integer, alunos_diferenciados integer);'
        );
    }
}
