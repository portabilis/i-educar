<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoTipoIncoerenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.tipo_incoerencia', function (Blueprint $table) {
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
        Schema::table('consistenciacao.tipo_incoerencia', function (Blueprint $table) {
            $table->dropForeign(['idcam']);
        });
    }
}
