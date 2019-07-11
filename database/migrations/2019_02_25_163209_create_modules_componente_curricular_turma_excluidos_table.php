<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesComponenteCurricularTurmaExcluidosTable extends Migration
{
    use WhenDeleted;

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

        $this->whenDeletedMoveTo('modules.componente_curricular_turma', 'modules.componente_curricular_turma_excluidos', [
            'componente_curricular_id',
            'ano_escolar_id',
            'escola_id',
            'turma_id',
            'carga_horaria',
            'docente_vinculado',
            'etapas_especificas',
            'etapas_utilizadas',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('modules.componente_curricular_turma');

        Schema::dropIfExists('modules.componente_curricular_turma_excluidos');
    }
}
