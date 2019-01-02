<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosFornecedorUnidadeAtendidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.fornecedor_unidade_atendida', function (Blueprint $table) {
            $table->foreign('iduni')
               ->references('iduni')
               ->on('alimentos.unidade_atendida');

            $table->foreign('idfor')
               ->references('idfor')
               ->on('alimentos.fornecedor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.fornecedor_unidade_atendida', function (Blueprint $table) {
            $table->dropForeign(['iduni']);
            $table->dropForeign(['idfor']);
        });
    }
}
