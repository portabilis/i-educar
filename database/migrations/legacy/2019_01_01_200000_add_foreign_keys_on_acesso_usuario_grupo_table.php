<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoUsuarioGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.usuario_grupo', function (Blueprint $table) {
            $table->foreign('login')
               ->references('login')
               ->on('acesso.usuario');

            $table->foreign('idgrp')
               ->references('idgrp')
               ->on('acesso.grupo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.usuario_grupo', function (Blueprint $table) {
            $table->dropForeign(['login']);
            $table->dropForeign(['idgrp']);
        });
    }
}
