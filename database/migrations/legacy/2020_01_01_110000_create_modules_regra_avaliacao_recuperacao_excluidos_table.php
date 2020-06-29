<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesRegraAvaliacaoRecuperacaoExcluidosTable extends Migration
{
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.regra_avaliacao_recuperacao_excluidos');
    }
}
