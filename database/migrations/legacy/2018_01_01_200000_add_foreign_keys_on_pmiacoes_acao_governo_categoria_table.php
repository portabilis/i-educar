<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiacoesAcaoGovernoCategoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiacoes.acao_governo_categoria', function (Blueprint $table) {
            $table->foreign('ref_cod_categoria')
               ->references('cod_categoria')
               ->on('pmiacoes.categoria')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_acao_governo')
               ->references('cod_acao_governo')
               ->on('pmiacoes.acao_governo')
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
        Schema::table('pmiacoes.acao_governo_categoria', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_categoria']);
            $table->dropForeign(['ref_cod_acao_governo']);
        });
    }
}
