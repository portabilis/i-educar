<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesEmpresaTransporteEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.empresa_transporte_escolar', function (Blueprint $table) {
            $table->foreign('ref_resp_idpes')
                ->references('idpes')
                ->on('cadastro.fisica')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_idpes')
                ->references('idpes')
                ->on('cadastro.juridica');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.empresa_transporte_escolar', function (Blueprint $table) {
            $table->dropForeign(['ref_resp_idpes']);
            $table->dropForeign(['ref_idpes']);
        });
    }
}
