<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInPmieducarDispensaDisciplinaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->whenDeletedMoveTo('pmieducar.dispensa_disciplina', 'pmieducar.dispensa_disciplina_excluidos', [
            'cod_dispensa',
            'ref_cod_matricula',
            'ref_cod_disciplina',
            'ref_cod_escola',
            'ref_cod_serie',
            'ref_usuario_exc',
            'ref_usuario_cad',
            'ref_cod_tipo_dispensa',
            'data_cadastro',
            'data_exclusao',
            'ativo',
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
        $this->dropTriggerWhenDeleted('pmieducar.dispensa_disciplina');
    }
}
