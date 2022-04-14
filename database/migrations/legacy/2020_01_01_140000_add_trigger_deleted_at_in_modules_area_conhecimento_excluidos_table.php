<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDeletedAtInModulesAreaConhecimentoExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->whenDeletedMoveTo('modules.area_conhecimento', 'modules.area_conhecimento_excluidos', [
            'id',
            'instituicao_id',
            'nome',
            'secao',
            'ordenamento_ac',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('modules.area_conhecimento');
    }
}
