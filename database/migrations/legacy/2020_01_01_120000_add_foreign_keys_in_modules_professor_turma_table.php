<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesProfessorTurmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->foreign('turno_id')
                ->references('id')
                ->on('pmieducar.turma_turno')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('turma_id')
                ->references('cod_turma')
                ->on('pmieducar.turma')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign(['servidor_id', 'instituicao_id'])
                ->references(['cod_servidor', 'ref_cod_instituicao'])
                ->on('pmieducar.servidor')
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
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->dropForeign(['turno_id']);
            $table->dropForeign(['turma_id']);
            $table->dropForeign(['servidor_id', 'instituicao_id']);
        });
    }
}
