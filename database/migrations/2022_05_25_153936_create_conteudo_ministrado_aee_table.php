<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConteudoMinistradoAeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.conteudo_ministrado_aee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_cod_matricula');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->text('atividades');
            $table->text('observacao')->nullable();
            $table->timestamp('data_cadastro');
            $table->timestamp('data_atualizacao')->nullable();
            $table->timestamps();
            //constraint            
            $table->foreign('ref_cod_matricula')->references('cod_matricula')->on('pmieducar.matricula')->onDelete(('cascade'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.conteudo_ministrado_aee');
    }
}
