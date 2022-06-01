<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanejamentoAulaAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula_aee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_cod_matricula');
            $table->unsignedBigInteger('ref_cod_turma');
            $table->date('data_inicial');
            $table->date('data_final');
            $table->integer('etapa_sequencial');
            $table->text('ddp');
            $table->timestamp('data_cadastro');
            $table->timestamp('data_atualizacao')->nullable();
            $table->text('recursos_didaticos')->nullable();
            $table->text('necessidade_aprendizagem')->nullable();
            $table->text('caracterizacao_pedagogica')->nullable();
            $table->text('outros')->nullable();
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
        Schema::dropIfExists('modules.planejamento_aula_aee');
    }
}
