<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosProdutoMedidaCaseiraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.produto_medida_caseira', function (Blueprint $table) {
            $table->foreign('idpro')
               ->references('idpro')
               ->on('alimentos.produto');

            $table->foreign(['idmedcas', 'idcli'])
               ->references(['idmedcas', 'idcli'])
               ->on('alimentos.medidas_caseiras');

            $table->foreign('idcli')
               ->references('idcli')
               ->on('alimentos.cliente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.produto_medida_caseira', function (Blueprint $table) {
            $table->dropForeign(['idpro']);
            $table->dropForeign(['idmedcas', 'idcli']);
            $table->dropForeign(['idcli']);
        });
    }
}
