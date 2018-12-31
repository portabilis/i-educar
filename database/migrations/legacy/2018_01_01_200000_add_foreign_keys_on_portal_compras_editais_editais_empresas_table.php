<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalComprasEditaisEditaisEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.compras_editais_editais_empresas', function (Blueprint $table) {
            $table->foreign('ref_cod_compras_editais_empresa')
               ->references('cod_compras_editais_empresa')
               ->on('portal.compras_editais_empresa')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_compras_editais_editais')
               ->references('cod_compras_editais_editais')
               ->on('portal.compras_editais_editais')
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
        Schema::table('portal.compras_editais_editais_empresas', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_compras_editais_empresa']);
            $table->dropForeign(['ref_cod_compras_editais_editais']);
        });
    }
}
