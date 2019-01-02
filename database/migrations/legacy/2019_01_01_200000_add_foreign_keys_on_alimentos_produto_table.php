<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosProdutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.produto', function (Blueprint $table) {
            $table->foreign(['idunp', 'idcli'])
               ->references(['idunp', 'idcli'])
               ->on('alimentos.unidade_produto');

            $table->foreign('idtip')
               ->references('idtip')
               ->on('alimentos.tipo_produto');

            $table->foreign('idfor')
               ->references('idfor')
               ->on('alimentos.fornecedor');

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
        Schema::table('alimentos.produto', function (Blueprint $table) {
            $table->dropForeign(['idunp', 'idcli']);
            $table->dropForeign(['idtip']);
            $table->dropForeign(['idfor']);
            $table->dropForeign(['idcli']);
        });
    }
}
