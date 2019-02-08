<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionAlimentosFcnCalcularQtdeUnidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/functions/alimentos.fcn_calcular_qtde_unidade.sql')
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
            'DROP FUNCTION alimentos.fcn_calcular_qtde_unidade(character varying, integer, integer, numeric, integer, integer);'
        );
    }
}
