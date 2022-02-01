<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTaughtBNCCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.conteudo_ministrado_bncc', function (Blueprint $table) {
            $table->id();
            $table->integer('conteudo_ministrado_id');
            $table->integer('bncc_id');
            
            $table->foreign('conteudo_ministrado_id')
                ->references('id')
                ->on('modules.conteudo_ministrado')
                ->onDelete('cascade');

            $table->foreign('bncc_id')
                ->references('id')
                ->on('modules.bncc')
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
        Schema::dropIfExists('modules.conteudo_ministrado_bncc');
    }
}
