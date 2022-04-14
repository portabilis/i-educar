<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTaughtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.conteudo_ministrado', function (Blueprint $table) {
            $table->id();
            $table->integer('frequencia_id');
            $table->text('procedimento_metodologico');
            $table->text('observacao')->nullable();
            $table->timestamp('data_cadastro');
            $table->timestamp('data_atualizacao')->nullable();

            $table->foreign('frequencia_id')
                ->references('id')
                ->on('modules.frequencia')
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
        Schema::dropIfExists('modules.conteudo_ministrado');
    }
}
