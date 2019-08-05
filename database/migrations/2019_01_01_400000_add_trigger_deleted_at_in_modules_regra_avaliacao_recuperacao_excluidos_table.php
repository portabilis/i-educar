<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInModulesRegraAvaliacaoRecuperacaoExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->whenDeletedMoveTo('modules.regra_avaliacao_recuperacao', 'modules.regra_avaliacao_recuperacao_excluidos', [
            'id',
            'regra_avaliacao_id',
            'descricao',
            'etapas_recuperadas',
            'substitui_menor_nota',
            'media',
            'nota_maxima',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('modules.regra_avaliacao_recuperacao');
    }
}
