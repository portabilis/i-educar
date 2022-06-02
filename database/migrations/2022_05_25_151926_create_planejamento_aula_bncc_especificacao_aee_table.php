<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanejamentoAulaBnccEspecificacaoAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_bncc_especificacao_aee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bncc_especificacao_id');
            $table->unsignedBigInteger('planejamento_aula_bncc_aee_id');
            $table->timestamps();
            //constraints
            $table->foreign('bncc_especificacao_id')->references('id')->on('modules.bncc_especificacao')->onDelete(('cascade'));
            $table->foreign('planejamento_aula_bncc_aee_id')->references('id')->on('modules.planejamento_aula_bncc_aee')->onDelete(('cascade'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.planejamento_aula_bncc_especificacao_aee');
    }
}
