<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedagogicalPlanningDiscipline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_componente_curricular', function (Blueprint $table) {
            $table->id();
            $table->integer('planejamento_aula_id');
            $table->integer('componente_curricular_id');
            
            $table->foreign('planejamento_aula_id')
                ->references('id')
                ->on('modules.planejamento_aula')
                ->onDelete('cascade');

            $table->foreign('componente_curricular_id')
                ->references('id')
                ->on('modules.componente_curricular')
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
        Schema::dropIfExists('modules.planejamento_aula_componente_curricular');
    }
}
