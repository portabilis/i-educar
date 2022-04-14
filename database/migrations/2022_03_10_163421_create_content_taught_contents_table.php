<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTaughtContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.conteudo_ministrado_conteudo', function (Blueprint $table) {
            $table->id();
            $table->integer('conteudo_ministrado_id');
            $table->integer('planejamento_aula_conteudo_id');
            
            $table->foreign('conteudo_ministrado_id')
                ->references('id')
                ->on('modules.conteudo_ministrado')
                ->onDelete('cascade');

            $table->foreign('planejamento_aula_conteudo_id')
                ->references('id')
                ->on('modules.planejamento_aula_conteudo')
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
        Schema::dropIfExists('modules.conteudo_ministrado_conteudo');
    }
}
