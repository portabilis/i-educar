<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiacoesAcaoGovernoArquivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiacoes.acao_governo_arquivo', function (Blueprint $table) {
            $table->foreign('ref_funcionario_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
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
        Schema::table('pmiacoes.acao_governo_arquivo', function (Blueprint $table) {
            $table->dropForeign(['ref_funcionario_cad']);
            $table->dropForeign(['ref_cod_acao_governo']);
        });
    }
}
