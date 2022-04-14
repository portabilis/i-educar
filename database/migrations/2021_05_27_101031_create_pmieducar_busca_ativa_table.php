<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarBuscaAtivaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.busca_ativa', function (Blueprint $table) {
            $table->id();
            $table->integer('ref_cod_matricula');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->smallInteger('resultado_busca_ativa')->default(2);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ref_cod_matricula')
                ->references('cod_matricula')
                ->on('pmieducar.matricula');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pmieducar.busca_ativa');
    }
}
