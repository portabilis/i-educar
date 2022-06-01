<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequenciaAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.frequencia_aee', function (Blueprint $table) {
            $table->id();
            $table->integer('ref_cod_turma');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->timestamp('data_cadastro');
            $table->timestamp('data_atualizacao')->nullable();
            $table->timestamps();
            //constraints
            $table->foreign('ref_cod_turma')->references('cod_turma')->on('pmieducar.turma')->onDelete(('cascade'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.frequencia_aee');
    }
}
