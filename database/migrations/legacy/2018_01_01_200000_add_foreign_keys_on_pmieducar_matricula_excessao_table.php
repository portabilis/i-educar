<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmieducarMatriculaExcessaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula_excessao', function (Blueprint $table) {
            $table->foreign(['ref_cod_matricula', 'ref_cod_turma', 'ref_sequencial'])
               ->references(['ref_cod_matricula', 'ref_cod_turma', 'sequencial'])
               ->on('pmieducar.matricula_turma')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
               ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
               ->on('pmieducar.escola_serie_disciplina')
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
        Schema::table('pmieducar.matricula_excessao', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_matricula', 'ref_cod_turma', 'ref_sequencial']);
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
        });
    }
}
