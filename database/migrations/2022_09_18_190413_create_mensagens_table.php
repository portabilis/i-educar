<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMensagensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public.mensagens', function (Blueprint $table) {
            $table->id();
            $table->integer('registro_id')->comment('Armazena ID que vincula ao plano de aula ou a frequência');
            $table->integer('emissor_user_id')->comment('Armazena ID do usuário que enviou a mensagem');
            $table->integer('receptor_user_id')->comment('Armazena ID do usuário que receberá a mensagem');
            $table->string('texto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensagens');
    }
}
