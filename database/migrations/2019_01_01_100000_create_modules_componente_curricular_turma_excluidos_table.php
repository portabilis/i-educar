<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularTurmaExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.componente_curricular_turma_excluidos', function (Blueprint $table) {
            $table->integer('componente_curricular_id');
            $table->integer('ano_escolar_id');
            $table->integer('escola_id');
            $table->integer('turma_id');
            $table->float('carga_horaria')->nullable();
            $table->integer('docente_vinculado')->nullable();
            $table->integer('etapas_especificas')->nullable();
            $table->string('etapas_utilizadas')->nullable();
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
        Schema::dropIfExists('modules.componente_curricular_turma_excluidos');
    }
}
