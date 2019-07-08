<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarCursoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.curso', function (Blueprint $table) {
            $table->foreign('ref_cod_tipo_regime')
               ->references('cod_tipo_regime')
               ->on('pmieducar.tipo_regime')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_tipo_ensino')
               ->references('cod_tipo_ensino')
               ->on('pmieducar.tipo_ensino')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_nivel_ensino')
               ->references('cod_nivel_ensino')
               ->on('pmieducar.nivel_ensino')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_instituicao')
               ->references('cod_instituicao')
               ->on('pmieducar.instituicao')
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
        Schema::table('pmieducar.curso', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_tipo_regime']);
            $table->dropForeign(['ref_cod_tipo_ensino']);
            $table->dropForeign(['ref_cod_nivel_ensino']);
            $table->dropForeign(['ref_cod_instituicao']);
        });
    }
}
