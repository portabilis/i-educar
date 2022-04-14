<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRelatorioViewHistorico9anosExtraCurricularView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_9anos_extra_curricular;'
        );

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/views/relatorio.view_historico_9anos_extra_curricular.sql')
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
            'DROP VIEW IF EXISTS relatorio.view_historico_9anos_extra_curricular;'
        );
    }
}
