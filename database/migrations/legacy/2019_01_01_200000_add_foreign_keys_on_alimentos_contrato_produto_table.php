<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosContratoProdutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.contrato_produto', function (Blueprint $table) {
            $table->foreign('idpro')
               ->references('idpro')
               ->on('alimentos.produto');

            $table->foreign('idcon')
               ->references('idcon')
               ->on('alimentos.contrato');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.contrato_produto', function (Blueprint $table) {
            $table->dropForeign(['idpro']);
            $table->dropForeign(['idcon']);
        });
    }
}
