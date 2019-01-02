<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoIncoerenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.incoerencia', function (Blueprint $table) {
            $table->foreign('idcon')
               ->references('idcon')
               ->on('consistenciacao.confrontacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.incoerencia', function (Blueprint $table) {
            $table->dropForeign(['idcon']);
        });
    }
}
