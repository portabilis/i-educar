<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularAnoEscolarExcluidosTable extends Migration
{
    use WhenDeleted;

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

        $this->whenDeletedMoveTo('modules.componente_curricular_ano_escolar', 'modules.componente_curricular_ano_escolar_excluidos', [
            'id',
            'ano',
            'instituicao_id',
            'turma_id',
            'servidor_id',
            'funcao_exercida',
            'tipo_vinculo',
            'permite_lancar_faltas_componente',
            'turno_id',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('modules.componente_curricular_ano_escolar');

        Schema::dropIfExists('modules.componente_curricular_ano_escolar_excluidos');
    }
}
