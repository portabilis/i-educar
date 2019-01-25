<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosCardapioReceitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.cardapio_receita', function (Blueprint $table) {
            $table->foreign('idrec')
               ->references('idrec')
               ->on('alimentos.receita');

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
        Schema::table('alimentos.cardapio_receita', function (Blueprint $table) {
            $table->dropForeign(['idrec']);
            $table->dropForeign(['idcar']);
        });
    }
}
