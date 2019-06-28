<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMatriculaTurmaExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.matricula_turma_excluidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ref_cod_matricula');
            $table->integer('ref_cod_turma');
            $table->integer('sequencial');
            $table->integer('ref_usuario_exc')->nullable();
            $table->integer('ref_usuario_cad');
            $table->timestamp('data_cadastro');
            $table->timestamp('data_exclusao')->nullable();
            $table->smallInteger('ativo');
            $table->date('data_enturmacao');
            $table->integer('sequencial_fechamento');
            $table->boolean('transferido')->nullable();
            $table->boolean('remanejado')->nullable();
            $table->boolean('reclassificado')->nullable();
            $table->boolean('abandono')->nullable();
            $table->boolean('falecido')->nullable();
            $table->smallInteger('etapa_educacenso')->nullable();
            $table->smallInteger('turma_unificada')->nullable();
            $table->integer('turno_id')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pmieducar.matricula_turma_excluidos');
    }
}
