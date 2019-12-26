<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesProfessorTurmaExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.professor_turma_excluidos', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('ano');
            $table->integer('instituicao_id');
            $table->integer('turma_id');
            $table->integer('servidor_id');
            $table->integer('funcao_exercida');
            $table->integer('tipo_vinculo');
            $table->integer('permite_lancar_faltas_componente')->nullable();
            $table->integer('turno_id')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('modules.professor_turma_excluidos');
    }
}
