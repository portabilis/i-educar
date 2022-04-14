<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarSerieVagaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.serie_vaga', function (Blueprint $table) {
            $table->foreign('ref_cod_serie')
                ->references('cod_serie')
                ->on('pmieducar.serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_instituicao')
                ->references('cod_instituicao')
                ->on('pmieducar.instituicao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_escola')
                ->references('cod_escola')
                ->on('pmieducar.escola')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_curso')
                ->references('cod_curso')
                ->on('pmieducar.curso')
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
        Schema::table('pmieducar.serie_vaga', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie']);
            $table->dropForeign(['ref_cod_instituicao']);
            $table->dropForeign(['ref_cod_escola']);
            $table->dropForeign(['ref_cod_curso']);
        });
    }
}
