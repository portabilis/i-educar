<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConteudoMinistradoConteudoAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.conteudo_ministrado_conteudo_aee', function (Blueprint $table) {
            $table->id();
            $table->integer('conteudo_ministrado_aee_id');
            $table->integer('planejamento_aula_conteudo_aee_id');
            $table->timestamps();
            //constraints
            $table->foreign('conteudo_ministrado_aee_id')->references('id')->on('modules.conteudo_ministrado_aee')->onDelete(('cascade')); 
            $table->foreign('planejamento_aula_conteudo_aee_id')->references('id')->on('modules.planejamento_aula_conteudo_aee')->onDelete(('cascade'));          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.conteudo_ministrado_conteudo_aee');
    }
}
