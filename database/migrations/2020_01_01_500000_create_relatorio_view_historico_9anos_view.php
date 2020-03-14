<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRelatorioViewHistorico9anosView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_9anos;'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/views/relatorio.view_historico_9anos.sql')
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
            'DROP VIEW IF EXISTS relatorio.view_historico_9anos;'
        );
    }
}
