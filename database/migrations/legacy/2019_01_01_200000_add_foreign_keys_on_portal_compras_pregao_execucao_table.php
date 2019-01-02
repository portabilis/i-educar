<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalComprasPregaoExecucaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.compras_pregao_execucao', function (Blueprint $table) {
            $table->foreign('ref_cod_compras_licitacoes')
               ->references('cod_compras_licitacoes')
               ->on('portal.compras_licitacoes')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_compras_final_pregao')
               ->references('cod_compras_final_pregao')
               ->on('portal.compras_final_pregao')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_equipe2')
               ->references('ref_ref_cod_pessoa_fj')
               ->on('portal.compras_funcionarios')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_equipe1')
               ->references('ref_ref_cod_pessoa_fj')
               ->on('portal.compras_funcionarios')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_pregoeiro')
               ->references('ref_ref_cod_pessoa_fj')
               ->on('portal.compras_funcionarios')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_equipe3')
               ->references('ref_ref_cod_pessoa_fj')
               ->on('portal.compras_funcionarios')
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
        Schema::table('portal.compras_pregao_execucao', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_compras_licitacoes']);
            $table->dropForeign(['ref_cod_compras_final_pregao']);
            $table->dropForeign(['ref_equipe2']);
            $table->dropForeign(['ref_equipe1']);
            $table->dropForeign(['ref_pregoeiro']);
            $table->dropForeign(['ref_equipe3']);
        });
    }
}
