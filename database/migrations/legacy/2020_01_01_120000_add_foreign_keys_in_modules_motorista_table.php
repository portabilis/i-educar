<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesMotoristaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.motorista', function (Blueprint $table) {
            $table->foreign('ref_idpes')
               ->references('idpes')
               ->on('cadastro.fisica')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_empresa_transporte_escolar')
               ->references('cod_empresa_transporte_escolar')
               ->on('modules.empresa_transporte_escolar')
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
        Schema::table('modules.motorista', function (Blueprint $table) {
            $table->dropForeign(['ref_idpes']);
            $table->dropForeign(['ref_cod_empresa_transporte_escolar']);
        });
    }
}
