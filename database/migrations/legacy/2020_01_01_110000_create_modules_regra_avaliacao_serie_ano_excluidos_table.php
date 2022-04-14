<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesRegraAvaliacaoSerieAnoExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.regra_avaliacao_serie_ano_excluidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('serie_id');
            $table->integer('regra_avaliacao_id');
            $table->integer('regra_avaliacao_diferenciada_id')->nullable();
            $table->integer('ano_letivo');
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
        Schema::dropIfExists('modules.regra_avaliacao_serie_ano_excluidos');
    }
}
