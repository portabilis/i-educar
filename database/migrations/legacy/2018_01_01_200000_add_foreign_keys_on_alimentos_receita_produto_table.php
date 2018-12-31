<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosReceitaProdutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.receita_produto', function (Blueprint $table) {
            $table->foreign('idrec')
               ->references('idrec')
               ->on('alimentos.receita');

            $table->foreign('idpro')
               ->references('idpro')
               ->on('alimentos.produto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.receita_produto', function (Blueprint $table) {
            $table->dropForeign(['idrec']);
            $table->dropForeign(['idpro']);
        });
    }
}
