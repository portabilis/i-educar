<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoCampoMetadadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.campo_metadado', function (Blueprint $table) {
            $table->foreign('idreg')
               ->references('idreg')
               ->on('consistenciacao.regra_campo');

            $table->foreign('idmet')
               ->references('idmet')
               ->on('consistenciacao.metadado');

            $table->foreign('idcam')
               ->references('idcam')
               ->on('consistenciacao.campo_consistenciacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.campo_metadado', function (Blueprint $table) {
            $table->dropForeign(['idreg']);
            $table->dropForeign(['idmet']);
            $table->dropForeign(['idcam']);
        });
    }
}
