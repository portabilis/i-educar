<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioGetQtdeAlunosSituacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.get_qtde_alunos_situacao.sql')
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.get_qtde_alunos_situacao2.sql')
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
            'DROP FUNCTION IF EXISTS relatorio.get_qtde_alunos_situacao(ano integer, instituicao integer, escola integer, curso integer, serie integer, turma integer, situacao integer, bairro integer, sexo character, idadeini integer, idadefim integer)'
        );

        DB::unprepared(
            'DROP FUNCTION IF EXISTS relatorio.get_qtde_alunos_situacao(integer, integer, character, integer, integer, integer);'
        );
    }
}
