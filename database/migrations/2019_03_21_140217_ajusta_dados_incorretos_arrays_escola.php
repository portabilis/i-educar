<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AjustaDadosIncorretosArraysEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE pmieducar.escola SET abastecimento_agua = ARRAY(SELECT UNNEST(abastecimento_agua[1:1])) WHERE array_ndims(abastecimento_agua) > 1;');
        DB::statement('UPDATE pmieducar.escola SET abastecimento_energia = ARRAY(SELECT UNNEST(abastecimento_energia[1:1])) WHERE array_ndims(abastecimento_energia) > 1;');
        DB::statement('UPDATE pmieducar.escola SET esgoto_sanitario = ARRAY(SELECT UNNEST(esgoto_sanitario[1:1])) WHERE array_ndims(esgoto_sanitario) > 1;');
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
