<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFunctionRelatorioGetValorCampoAuditoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/functions/relatorio.get_valor_campo_auditoria.sql')
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
            'DROP FUNCTION relatorio.get_valor_campo_auditoria(character varying, character varying, character varying);'
        );
    }
}
