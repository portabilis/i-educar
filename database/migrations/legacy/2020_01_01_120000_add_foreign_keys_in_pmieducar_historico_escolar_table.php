<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarHistoricoEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.historico_escolar', function (Blueprint $table) {
            $table->foreign('historico_grade_curso_id')
                ->references('id')
                ->on('pmieducar.historico_grade_curso')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_escola')
                ->references('cod_escola')
                ->on('pmieducar.escola');

            $table->foreign('ref_cod_aluno')
                ->references('cod_aluno')
                ->on('pmieducar.aluno')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.historico_escolar', function (Blueprint $table) {
            $table->dropForeign(['historico_grade_curso_id']);
            $table->dropForeign(['ref_cod_escola']);
            $table->dropForeign(['ref_cod_aluno']);
        });
    }
}
