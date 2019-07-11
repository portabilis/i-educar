<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarDisciplinaDependenciaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.disciplina_dependencia_excluidos', function (Blueprint $table) {
            $table->integer('cod_disciplina_dependencia');
            $table->integer('ref_cod_matricula');
            $table->integer('ref_cod_disciplina');
            $table->integer('ref_cod_escola');
            $table->integer('ref_cod_serie');
            $table->text('observacao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->whenDeletedMoveTo('pmieducar.disciplina_dependencia', 'pmieducar.disciplina_dependencia_excluidos', [
            'cod_disciplina_dependencia',
            'ref_cod_matricula',
            'ref_cod_disciplina',
            'ref_cod_escola',
            'ref_cod_serie',
            'observacao',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('pmieducar.disciplina_dependencia');

        Schema::dropIfExists('pmieducar.disciplina_dependencia_excluidos');
    }
}
