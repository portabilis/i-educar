<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBuscaAtiva extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.busca_ativa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ref_cod_matricula')->nullable();
            $table->timestamp('data_inicio')->nullable();
            $table->timestamp('data_fim')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->boolean('ativo')->default(false);
            $table->string('observacoes')->nullable();
            $table->string('resultado_busca_ativa')->nullable();
            $table->timestamps();

            $table->foreign('ref_cod_matricula')->references('cod_matricula')->on('pmieducar.matricula');
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
