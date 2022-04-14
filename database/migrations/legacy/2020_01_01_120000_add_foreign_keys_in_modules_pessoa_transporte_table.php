<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesPessoaTransporteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.pessoa_transporte', function (Blueprint $table) {
            $table->foreign('ref_idpes')
                ->references('idpes')
                ->on('cadastro.fisica')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_idpes_destino')
                ->references('idpes')
                ->on('cadastro.juridica');

            $table->foreign('ref_cod_rota_transporte_escolar')
                ->references('cod_rota_transporte_escolar')
                ->on('modules.rota_transporte_escolar');

            $table->foreign('ref_cod_ponto_transporte_escolar')
                ->references('cod_ponto_transporte_escolar')
                ->on('modules.ponto_transporte_escolar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.pessoa_transporte', function (Blueprint $table) {
            $table->dropForeign(['ref_idpes']);
            $table->dropForeign(['ref_idpes_destino']);
            $table->dropForeign(['ref_cod_rota_transporte_escolar']);
            $table->dropForeign(['ref_cod_ponto_transporte_escolar']);
        });
    }
}
