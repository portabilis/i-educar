<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlteraDataEducacenso2020V2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.instituicao')
            ->where('ativo', 1)
            ->update(['data_educacenso' => '2020-03-11']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('pmieducar.instituicao')
            ->where('ativo', 1)
            ->update(['data_educacenso' => '2020-05-27']);
    }
}
