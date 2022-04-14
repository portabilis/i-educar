<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedagogicalPlanningBNCCSpecifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_bncc_especificacao', function (Blueprint $table) {
            $table->id();
            $table->integer('planejamento_aula_bncc_id');
            $table->integer('bncc_especificacao_id');
            
            $table->foreign('planejamento_aula_bncc_id')
                ->references('id')
                ->on('modules.planejamento_aula_bncc')
                ->onDelete('cascade');

            $table->foreign('bncc_especificacao_id')
                ->references('id')
                ->on('modules.bncc_especificacao')
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
        Schema::dropIfExists('modules.planejamento_aula_bncc_especificacao');
    }
}
