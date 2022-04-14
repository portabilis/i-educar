<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionPmieducarMigraBeneficiosParaTabelaAlunoAlunoBeneficio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/functions/pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio.sql')
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
            'DROP FUNCTION pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio();'
        );
    }
}
