<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoGrupoFuncaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.grupo_funcao', function (Blueprint $table) {
            $table->foreign(['idgrp', 'idsis', 'idmen'])
               ->references(['idgrp', 'idsis', 'idmen'])
               ->on('acesso.grupo_menu');

            $table->foreign(['idfunc', 'idsis', 'idmen'])
               ->references(['idfunc', 'idsis', 'idmen'])
               ->on('acesso.funcao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.grupo_funcao', function (Blueprint $table) {
            $table->dropForeign(['idgrp', 'idsis', 'idmen']);
            $table->dropForeign(['idfunc', 'idsis', 'idmen']);
        });
    }
}
