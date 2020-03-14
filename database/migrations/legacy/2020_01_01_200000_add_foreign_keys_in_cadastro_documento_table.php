<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInCadastroDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->foreign('sigla_uf_exp_rg')
               ->references('sigla_uf')
               ->on('uf');

            $table->foreign('sigla_uf_cert_civil')
               ->references('sigla_uf')
               ->on('uf');

            $table->foreign('sigla_uf_cart_trabalho')
               ->references('sigla_uf')
               ->on('uf');

            $table->foreign('idpes_rev')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes_cad')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idorg_exp_rg')
               ->references('idorg_rg')
               ->on('cadastro.orgao_emissor_rg')
               ->onDelete('restrict');

            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.fisica');

            $table->foreign('cartorio_cert_civil_inep')
               ->references('id')
               ->on('cadastro.codigo_cartorio_inep')
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
        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->dropForeign(['sigla_uf_exp_rg']);
            $table->dropForeign(['sigla_uf_cert_civil']);
            $table->dropForeign(['sigla_uf_cart_trabalho']);
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
            $table->dropForeign(['idorg_exp_rg']);
            $table->dropForeign(['idpes']);
            $table->dropForeign(['cartorio_cert_civil_inep']);
        });
    }
}
