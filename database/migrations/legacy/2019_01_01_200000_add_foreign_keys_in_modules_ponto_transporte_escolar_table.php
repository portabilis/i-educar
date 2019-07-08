<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesPontoTransporteEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.ponto_transporte_escolar', function (Blueprint $table) {
            $table->foreign(['idbai', 'idlog', 'cep'])
               ->references(['idbai', 'idlog', 'cep'])
               ->on('urbano.cep_logradouro_bairro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.ponto_transporte_escolar', function (Blueprint $table) {
            $table->dropForeign(['idbai', 'idlog', 'cep']);
        });
    }
}
