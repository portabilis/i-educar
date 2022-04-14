<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesVeiculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.veiculo', function (Blueprint $table) {
            $table->foreign('ref_cod_tipo_veiculo')
                ->references('cod_tipo_veiculo')
                ->on('modules.tipo_veiculo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_empresa_transporte_escolar')
                ->references('cod_empresa_transporte_escolar')
                ->on('modules.empresa_transporte_escolar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.veiculo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_tipo_veiculo']);
            $table->dropForeign(['ref_cod_empresa_transporte_escolar']);
        });
    }
}
