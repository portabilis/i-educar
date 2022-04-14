<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarTurmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->foreign('turma_turno_id')
                ->references('id')
                ->on('pmieducar.turma_turno')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_turma_tipo')
                ->references('cod_turma_tipo')
                ->on('pmieducar.turma_tipo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign(['ref_ref_cod_escola', 'ref_ref_cod_serie'])
                ->references(['ref_cod_escola', 'ref_cod_serie'])
                ->on('pmieducar.escola_serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign(['ref_cod_regente', 'ref_cod_instituicao_regente'])
                ->references(['cod_servidor', 'ref_cod_instituicao'])
                ->on('pmieducar.servidor')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_instituicao')
                ->references('cod_instituicao')
                ->on('pmieducar.instituicao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_infra_predio_comodo')
                ->references('cod_infra_predio_comodo')
                ->on('pmieducar.infra_predio_comodo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_curso')
                ->references('cod_curso')
                ->on('pmieducar.curso')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign(['ref_ref_cod_serie_mult', 'ref_ref_cod_escola_mult'])
                ->references(['ref_cod_serie', 'ref_cod_escola'])
                ->on('pmieducar.escola_serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_disciplina_dispensada')
                ->references('id')
                ->on('modules.componente_curricular');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropForeign(['turma_turno_id']);
            $table->dropForeign(['ref_cod_turma_tipo']);
            $table->dropForeign(['ref_ref_cod_escola', 'ref_ref_cod_serie']);
            $table->dropForeign(['ref_cod_regente', 'ref_cod_instituicao_regente']);
            $table->dropForeign(['ref_cod_instituicao']);
            $table->dropForeign(['ref_cod_infra_predio_comodo']);
            $table->dropForeign(['ref_cod_curso']);
            $table->dropForeign(['ref_ref_cod_serie_mult', 'ref_ref_cod_escola_mult']);
            $table->dropForeign(['ref_cod_disciplina_dispensada']);
        });
    }
}
