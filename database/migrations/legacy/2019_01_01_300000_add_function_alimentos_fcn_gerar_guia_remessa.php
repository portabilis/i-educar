<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionAlimentosFcnGerarGuiaRemessa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../../sqls/functions/alimentos.fcn_gerar_guia_remessa.sql')
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
            'DROP FUNCTION alimentos.fcn_gerar_guia_remessa(text, text, integer, integer, character varying, character varying, character varying, integer);'
        );
    }
}
