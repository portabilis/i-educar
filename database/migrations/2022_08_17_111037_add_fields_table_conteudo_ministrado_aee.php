<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsTableConteudoMinistradoAee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.conteudo_ministrado_aee', function (Blueprint $table) {
            $table->time('hora_inicio');
            $table->time('hora_fim');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.conteudo_ministrado_aee', function (Blueprint $table) {
            $table->time('hora_inicio');
            $table->time('hora_fim');
        });
    }
}
