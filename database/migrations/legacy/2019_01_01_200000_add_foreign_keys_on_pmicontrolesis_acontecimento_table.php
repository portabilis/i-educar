<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmicontrolesisAcontecimentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmicontrolesis.acontecimento', function (Blueprint $table) {
            $table->foreign('ref_cod_tipo_acontecimento')
               ->references('cod_tipo_acontecimento')
               ->on('pmicontrolesis.tipo_acontecimento');

            $table->foreign('ref_cod_funcionario_exc')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario');

            $table->foreign('ref_cod_funcionario_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmicontrolesis.acontecimento', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_tipo_acontecimento']);
            $table->dropForeign(['ref_cod_funcionario_exc']);
            $table->dropForeign(['ref_cod_funcionario_cad']);
        });
    }
}
