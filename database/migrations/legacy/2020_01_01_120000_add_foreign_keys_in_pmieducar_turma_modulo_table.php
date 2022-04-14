<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarTurmaModuloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.turma_modulo', function (Blueprint $table) {
            $table->foreign('ref_cod_turma')
                ->references('cod_turma')
                ->on('pmieducar.turma')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_modulo')
                ->references('cod_modulo')
                ->on('pmieducar.modulo')
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
        Schema::table('pmieducar.turma_modulo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_turma']);
            $table->dropForeign(['ref_cod_modulo']);
        });
    }
}
