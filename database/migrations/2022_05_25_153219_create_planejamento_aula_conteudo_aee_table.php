<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanejamentoAulaConteudoAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_conteudo_aee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planejamento_aula_aee_id');
            $table->text('conteudo');
            $table->timestamps();
            //constraint
            $table->foreign('planejamento_aula_aee_id')->references('id')->on('modules.planejamento_aula_aee')->onDelete(('cascade'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.planejamento_aula_conteudo_aee');
    }
}
