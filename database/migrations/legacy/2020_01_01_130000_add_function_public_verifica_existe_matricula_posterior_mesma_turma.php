<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionPublicVerificaExisteMatriculaPosteriorMesmaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/functions/public.verifica_existe_matricula_posterior_mesma_turma.sql')
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
            'DROP FUNCTION public.verifica_existe_matricula_posterior_mesma_turma(cod_matricula integer, cod_turma integer);'
        );
    }
}
