<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.usuario', function (Blueprint $table) {
            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.pessoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.usuario', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
        });
    }
}
