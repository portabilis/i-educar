<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarCalendarioDiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.calendario_dia', function (Blueprint $table) {
            $table->foreign('ref_cod_calendario_dia_motivo')
                ->references('cod_calendario_dia_motivo')
                ->on('pmieducar.calendario_dia_motivo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_calendario_ano_letivo')
                ->references('cod_calendario_ano_letivo')
                ->on('pmieducar.calendario_ano_letivo')
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
        Schema::table('pmieducar.calendario_dia', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_calendario_dia_motivo']);
            $table->dropForeign(['ref_cod_calendario_ano_letivo']);
        });
    }
}
