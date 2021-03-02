<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarDispensaDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.dispensa_disciplina', function (Blueprint $table) {
            $table->foreign('ref_cod_tipo_dispensa')
               ->references('cod_tipo_dispensa')
               ->on('pmieducar.tipo_dispensa')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_matricula')
               ->references('cod_matricula')
               ->on('pmieducar.matricula')
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
        Schema::table('pmieducar.dispensa_disciplina', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_tipo_dispensa']);
            $table->dropForeign(['ref_cod_matricula']);
        });
    }
}
