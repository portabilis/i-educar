<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInPmieducarDisciplinaDependenciaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
    }
}
