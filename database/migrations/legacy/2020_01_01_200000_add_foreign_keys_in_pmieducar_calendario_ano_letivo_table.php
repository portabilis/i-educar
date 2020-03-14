<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarCalendarioAnoLetivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.calendario_ano_letivo', function (Blueprint $table) {
            $table->foreign('ref_cod_escola')
               ->references('cod_escola')
               ->on('pmieducar.escola')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.calendario_ano_letivo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_escola']);
        });
    }
}
