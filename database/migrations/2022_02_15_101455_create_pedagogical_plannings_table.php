<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedagogicalPlanningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.planejamento_aula', function (Blueprint $table) {
            $table->id();
            $table->date('data_inicial');
            $table->date('data_final');
            $table->integer('ref_cod_turma');
            $table->integer('ref_componente_curricular');
            $table->integer('etapa_sequencial');
            $table->text('ddp');
            $table->text('atividades');
            $table->timestamp('data_cadastro');
            $table->timestamp('data_atualizacao')->nullable();

            $table->foreign('ref_cod_turma')
                ->references('cod_turma')
                ->on('pmieducar.turma')
                ->onDelete('cascade');

            $table->foreign('ref_componente_curricular')
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
        Schema::dropIfExists('modules.planejamento_aula');
    }
}
