<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequenciaAlunoAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.frequencia_aluno_aee', function (Blueprint $table) {
            $table->id();
            $table->integer('frequencia_aee_id');
            $table->char('justificativa', 191);           
            $table->timestamps();
            //constraint
            $table->foreign('frequencia_aee_id')->references('id')->on('modules.frequencia_aee')->onDelete(('cascade'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.frequencia_aluno_aee');
    }
}
