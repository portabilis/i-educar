<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosCardapioFaixaUnidadeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.cardapio_faixa_unidade', function (Blueprint $table) {
            $table->foreign('idfeu')
               ->references('idfeu')
               ->on('alimentos.unidade_faixa_etaria');

            $table->foreign('idcar')
               ->references('idcar')
               ->on('alimentos.cardapio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.cardapio_faixa_unidade', function (Blueprint $table) {
            $table->dropForeign(['idfeu']);
            $table->dropForeign(['idcar']);
        });
    }
}
