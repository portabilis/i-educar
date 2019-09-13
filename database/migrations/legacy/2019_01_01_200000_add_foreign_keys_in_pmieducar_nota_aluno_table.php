<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarNotaAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.nota_aluno', function (Blueprint $table) {
            $table->foreign(['ref_ref_cod_tipo_avaliacao', 'ref_sequencial'])
               ->references(['ref_cod_tipo_avaliacao', 'sequencial'])
               ->on('pmieducar.tipo_avaliacao_valores')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
               ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
               ->on('pmieducar.escola_serie_disciplina')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_matricula')
               ->references('cod_matricula')
               ->on('pmieducar.matricula')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_curso_disciplina')
               ->references('cod_disciplina')
               ->on('pmieducar.disciplina')
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
        Schema::table('pmieducar.nota_aluno', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_tipo_avaliacao', 'ref_sequencial']);
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
            $table->dropForeign(['ref_cod_matricula']);
            $table->dropForeign(['ref_cod_curso_disciplina']);
        });
    }
}
