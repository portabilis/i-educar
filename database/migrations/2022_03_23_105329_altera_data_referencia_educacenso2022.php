<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlteraDataReferenciaEducacenso2022 extends Migration
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
            ->update(['data_educacenso' => '2022-05-25']);
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
            ->update(['data_educacenso' => '2021-05-26']);
    }
}
