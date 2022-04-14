<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesAreaConhecimentoExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.area_conhecimento_excluidos', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('instituicao_id');
            $table->string('nome');
            $table->string('secao')->nullable();
            $table->integer('ordenamento_ac')->nullable();
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
        Schema::dropIfExists('modules.area_conhecimento_excluidos');
    }
}
