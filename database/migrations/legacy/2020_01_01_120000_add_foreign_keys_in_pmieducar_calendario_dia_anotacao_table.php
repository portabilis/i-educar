<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarCalendarioDiaAnotacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.calendario_dia_anotacao', function (Blueprint $table) {
            $table->foreign(['ref_ref_cod_calendario_ano_letivo', 'ref_mes', 'ref_dia'])
                ->references(['ref_cod_calendario_ano_letivo', 'mes', 'dia'])
                ->on('pmieducar.calendario_dia')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_calendario_anotacao')
                ->references('cod_calendario_anotacao')
                ->on('pmieducar.calendario_anotacao')
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
        Schema::table('pmieducar.calendario_dia_anotacao', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_calendario_ano_letivo', 'ref_mes', 'ref_dia']);
            $table->dropForeign(['ref_cod_calendario_anotacao']);
        });
    }
}
