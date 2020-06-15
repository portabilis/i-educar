<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesItinerarioTransporteEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.itinerario_transporte_escolar', function (Blueprint $table) {
            $table->foreign('ref_cod_veiculo')
               ->references('cod_veiculo')
               ->on('modules.veiculo')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_rota_transporte_escolar')
               ->references('cod_rota_transporte_escolar')
               ->on('modules.rota_transporte_escolar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.itinerario_transporte_escolar', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_veiculo']);
            $table->dropForeign(['ref_cod_rota_transporte_escolar']);
        });
    }
}
