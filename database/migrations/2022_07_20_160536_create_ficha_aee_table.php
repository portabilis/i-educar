<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichaAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.ficha_aee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_cod_matricula');
            $table->unsignedBigInteger('ref_cod_turma');
            $table->date('data');
            $table->text('necessidades_aprendizagem')->nullable();
            $table->text('caracterizacao_pedagogica')->nullable();
            $table->timestamps();
            //constraint
            $table->foreign('ref_cod_matricula')->references('cod_matricula')->on('pmieducar.matricula')->onDelete(('cascade'));
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
        Schema::dropIfExists('modules.ficha_aee');
    }
}
