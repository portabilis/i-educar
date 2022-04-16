<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTaughtSpecification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.conteudo_ministrado_bncc_especificacao', function (Blueprint $table) {
            $table->id();
            $table->integer('conteudo_ministrado_id');
            $table->integer('planejamento_aula_bncc_especificacao_id');
            
            $table->foreign('conteudo_ministrado_id')
                ->references('id')
                ->on('modules.conteudo_ministrado')
                ->onDelete('cascade');

            $table->foreign('planejamento_aula_bncc_especificacao_id')
                ->references('id')
                ->on('modules.planejamento_aula_bncc_especificacao')
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
        Schema::dropIfExists('modules.conteudo_ministrado_bncc_especificacao');
    }
}
