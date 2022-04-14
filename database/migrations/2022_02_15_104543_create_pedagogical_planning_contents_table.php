<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedagogicalPlanningContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_conteudo', function (Blueprint $table) {
            $table->id();
            $table->integer('planejamento_aula_id');
            $table->text('conteudo');
            
            $table->foreign('planejamento_aula_id')
                ->references('id')
                ->on('modules.planejamento_aula')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.planejamento_aula_conteudo');
    }
}
