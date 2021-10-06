<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarQuadroHorarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.quadro_horario', function (Blueprint $table) {
            $table->foreign('ref_cod_turma')
               ->references('cod_turma')
               ->on('pmieducar.turma')
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
        Schema::table('pmieducar.quadro_horario', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_turma']);
        });
    }
}
