<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoHistoricoCampoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.historico_campo', function (Blueprint $table) {
            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('cascade');

            $table->foreign('idcam')
               ->references('idcam')
               ->on('consistenciacao.campo_consistenciacao')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.historico_campo', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
            $table->dropForeign(['idcam']);
        });
    }
}
