<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AlteraViewSituacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = file_get_contents(database_path('sqls/views/relatorio.view_situacao.sql'));

        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = file_get_contents(database_path('sqls/views/relatorio.view_situacao-2019-09-26.sql'));

        DB::unprepared($sql);
    }
}
