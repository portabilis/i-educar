<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesTabelaArredondamentoValorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.tabela_arredondamento_valor', function (Blueprint $table) {
            $table->foreign('tabela_arredondamento_id')
               ->references('id')
               ->on('modules.tabela_arredondamento')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.tabela_arredondamento_valor', function (Blueprint $table) {
            $table->dropForeign(['tabela_arredondamento_id']);
        });
    }
}
