<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInPmieducarEscolaSerieDisciplinaExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->whenDeletedMoveTo('pmieducar.escola_serie_disciplina', 'pmieducar.escola_serie_disciplina_excluidos', [
            'ref_ref_cod_serie',
            'ref_ref_cod_escola',
            'ref_cod_disciplina',
            'ativo',
            'carga_horaria',
            'etapas_especificas',
            'etapas_utilizadas',
            'anos_letivos',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('pmieducar.escola_serie_disciplina');
    }
}
