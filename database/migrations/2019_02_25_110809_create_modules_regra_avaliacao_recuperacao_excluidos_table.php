<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesRegraAvaliacaoRecuperacaoExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.regra_avaliacao_recuperacao_excluidos', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('regra_avaliacao_id');
            $table->string('descricao');
            $table->string('etapas_recuperadas');
            $table->boolean('substitui_menor_nota')->nullable();
            $table->float('media');
            $table->float('nota_maxima');
            $table->timestamps();
            $table->softDeletes();
        });

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

        Schema::dropIfExists('modules.regra_avaliacao_recuperacao_excluidos');
    }
}
