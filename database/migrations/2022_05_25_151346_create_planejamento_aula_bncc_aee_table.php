<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanejamentoAulaBnccAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_bncc_aee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bncc_id');
            $table->unsignedBigInteger('planejamento_aula_aee_id');
            $table->timestamps();
            //constraints
            $table->foreign('bncc_id')->references('id')->on('modules.bncc')->onDelete(('cascade'));
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
        Schema::dropIfExists('modules.planejamento_aula_bncc_aee');
    }
}
