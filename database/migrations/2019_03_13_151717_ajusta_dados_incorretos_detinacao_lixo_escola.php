<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AjustaDadosIncorretosDetinacaoLixoEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE pmieducar.escola SET destinacao_lixo = ARRAY(SELECT UNNEST(destinacao_lixo[1:1])) WHERE array_ndims(destinacao_lixo) > 1;');
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
