<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoOperacaoFuncaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.operacao_funcao', function (Blueprint $table) {
            $table->foreign('idope')
               ->references('idope')
               ->on('acesso.operacao');

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
        Schema::table('acesso.operacao_funcao', function (Blueprint $table) {
            $table->dropForeign(['idope']);
            $table->dropForeign(['idfunc', 'idsis', 'idmen']);
        });
    }
}
