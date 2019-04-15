<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AjustaValoresAbastecimentoEnergia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE pmieducar.escola
                                SET abastecimento_energia = NULL
                              WHERE 2 = ANY (abastecimento_energia)
                                 OR 3 = ANY (abastecimento_energia)');
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
