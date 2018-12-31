<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoFuncaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.funcao', function (Blueprint $table) {
            $table->foreign(['idmen', 'idsis'])
               ->references(['idmen', 'idsis'])
               ->on('acesso.menu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.funcao', function (Blueprint $table) {
            $table->dropForeign(['idmen', 'idsis']);
        });
    }
}
