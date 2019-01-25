<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiacoesSecretariaResponsavelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiacoes.secretaria_responsavel', function (Blueprint $table) {
            $table->foreign('ref_cod_setor')
               ->references('cod_setor')
               ->on('pmidrh.setor')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_funcionario_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
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
        Schema::table('pmiacoes.secretaria_responsavel', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_setor']);
            $table->dropForeign(['ref_cod_funcionario_cad']);
        });
    }
}
