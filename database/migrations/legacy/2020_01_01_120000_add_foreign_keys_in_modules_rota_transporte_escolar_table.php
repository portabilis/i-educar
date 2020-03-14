<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesRotaTransporteEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.rota_transporte_escolar', function (Blueprint $table) {
            $table->foreign('ref_idpes_destino')
               ->references('idpes')
               ->on('cadastro.juridica');

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
        Schema::table('modules.rota_transporte_escolar', function (Blueprint $table) {
            $table->dropForeign(['ref_idpes_destino']);
            $table->dropForeign(['ref_cod_empresa_transporte_escolar']);
        });
    }
}
