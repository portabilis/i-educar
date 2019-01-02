<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoGrupoMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.grupo_menu', function (Blueprint $table) {
            $table->foreign(['idmen', 'idsis'])
               ->references(['idmen', 'idsis'])
               ->on('acesso.menu');

            $table->foreign(['idsis', 'idgrp'])
               ->references(['idsis', 'idgrp'])
               ->on('acesso.grupo_sistema');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.grupo_menu', function (Blueprint $table) {
            $table->dropForeign(['idmen', 'idsis']);
            $table->dropForeign(['idsis', 'idgrp']);
        });
    }
}
