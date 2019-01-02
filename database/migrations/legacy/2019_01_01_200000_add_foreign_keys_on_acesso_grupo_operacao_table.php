<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoGrupoOperacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.grupo_operacao', function (Blueprint $table) {
            $table->foreign(['idmen', 'idsis', 'idfunc', 'idope'])
               ->references(['idmen', 'idsis', 'idfunc', 'idope'])
               ->on('acesso.operacao_funcao');

            $table->foreign(['idmen', 'idsis', 'idgrp', 'idfunc'])
               ->references(['idmen', 'idsis', 'idgrp', 'idfunc'])
               ->on('acesso.grupo_funcao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.grupo_operacao', function (Blueprint $table) {
            $table->dropForeign(['idmen', 'idsis', 'idfunc', 'idope']);
            $table->dropForeign(['idmen', 'idsis', 'idgrp', 'idfunc']);
        });
    }
}
