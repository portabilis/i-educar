<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CorrigeViewDeHistoricosExtraCurriculares extends Migration
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
            file_get_contents(__DIR__ . '/../sqls/views/relatorio.view_historico_9anos_extra_curricular-2021-12-06.sql')
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

        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/views/relatorio.view_historico_9anos_extra_curricular-2021-12-05.sql')
        );
    }
}
