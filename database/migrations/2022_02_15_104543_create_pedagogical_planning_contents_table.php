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
        Schema::create('modules.planejamento_pedagogico_conteudo', function (Blueprint $table) {
            $table->id();
            $table->integer('planejamento_pedagogico_id');
            $table->text('conteudo');
            
            $table->foreign('planejamento_pedagogico_id')
                ->references('id')
                ->on('modules.planejamento_pedagogico')
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
        Schema::dropIfExists('modules.planejamento_pedagogico_conteudo');
    }
}
