<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoGrupoSistemaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.grupo_sistema', function (Blueprint $table) {
            $table->foreign('idsis')
               ->references('idsis')
               ->on('acesso.sistema');

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
        Schema::table('acesso.grupo_sistema', function (Blueprint $table) {
            $table->dropForeign(['idsis']);
            $table->dropForeign(['idgrp']);
        });
    }
}
