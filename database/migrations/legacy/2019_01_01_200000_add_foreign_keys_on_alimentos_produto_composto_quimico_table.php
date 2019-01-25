<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosProdutoCompostoQuimicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.produto_composto_quimico', function (Blueprint $table) {
            $table->foreign('idpro')
               ->references('idpro')
               ->on('alimentos.produto');

            $table->foreign('idcom')
               ->references('idcom')
               ->on('alimentos.composto_quimico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.produto_composto_quimico', function (Blueprint $table) {
            $table->dropForeign(['idpro']);
            $table->dropForeign(['idcom']);
        });
    }
}
