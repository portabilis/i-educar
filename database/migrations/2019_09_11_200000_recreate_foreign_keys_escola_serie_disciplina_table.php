<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateForeignKeysEscolaSerieDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula_excessao', function (Blueprint $table) {
            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
                ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
                ->on('pmieducar.escola_serie_disciplina')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        Schema::table('pmieducar.quadro_horario_horarios_aux', function (Blueprint $table) {
            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
                ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
                ->on('pmieducar.escola_serie_disciplina')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        Schema::table('pmieducar.disciplina_dependencia', function (Blueprint $table) {
            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
                ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
                ->on('pmieducar.escola_serie_disciplina')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        Schema::table('pmieducar.nota_aluno', function (Blueprint $table) {
            $table->foreign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina'])
                ->references(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina'])
                ->on('pmieducar.escola_serie_disciplina')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        Schema::table('pmieducar.falta_aluno', function (Blueprint $table) {
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
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
        });

        Schema::table('pmieducar.quadro_horario_horarios_aux', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
        });

        Schema::table('pmieducar.disciplina_dependencia', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
        });

        Schema::table('pmieducar.nota_aluno', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
        });

        Schema::table('pmieducar.falta_aluno', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina']);
        });
    }
}
