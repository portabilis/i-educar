<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesProfessorTurmaDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.professor_turma_disciplina', function (Blueprint $table) {
            $table->foreign('professor_turma_id')
               ->references('id')
               ->on('modules.professor_turma')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('componente_curricular_id')
               ->references('id')
               ->on('modules.componente_curricular')
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
        Schema::table('modules.professor_turma_disciplina', function (Blueprint $table) {
            $table->dropForeign(['professor_turma_id']);
            $table->dropForeign(['componente_curricular_id']);
        });
    }
}
