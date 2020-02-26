<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class KeepJustOneConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $config = (array) DB::table('pmieducar.configuracoes_gerais')->first();

        DB::unprepared('DELETE FROM pmieducar.configuracoes_gerais;');

        DB::table('pmieducar.configuracoes_gerais')->insert($config);
    }
}
