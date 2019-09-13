<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularAnoEscolarExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.componente_curricular_ano_escolar_excluidos', function (Blueprint $table) {
            $table->integer('id')->index();
            $table->integer('ano');
            $table->integer('instituicao_id');
            $table->integer('turma_id');
            $table->integer('servidor_id');
            $table->integer('funcao_exercida');
            $table->integer('tipo_vinculo')->nullable();
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
        Schema::dropIfExists('modules.componente_curricular_ano_escolar_excluidos');
    }
}
