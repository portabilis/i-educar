<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoOcorrenciaRegraCampoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.ocorrencia_regra_campo', function (Blueprint $table) {
            $table->foreign('idreg')
               ->references('idreg')
               ->on('consistenciacao.regra_campo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.ocorrencia_regra_campo', function (Blueprint $table) {
            $table->dropForeign(['idreg']);
        });
    }
}
