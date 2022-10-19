<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldServidorIdTableConteudoMinistradoAee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.conteudo_ministrado_aee', function (Blueprint $table) {
            $table->integer('servidor_id')->nullable();
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
            //
        });
    }
}
