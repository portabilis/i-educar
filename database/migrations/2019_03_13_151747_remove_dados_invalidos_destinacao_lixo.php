<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class RemoveDadosInvalidosDestinacaoLixo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE pmieducar.escola SET destinacao_lixo = array_remove(destinacao_lixo, 4)');
        DB::statement('UPDATE pmieducar.escola SET destinacao_lixo = array_remove(destinacao_lixo, 6)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
