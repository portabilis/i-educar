<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.menu', function (Blueprint $table) {
            $table->foreign('idsis')
               ->references('idsis')
               ->on('acesso.sistema');

            $table->foreign(['menu_idsis', 'menu_idmen'])
               ->references(['idsis', 'idmen'])
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
        Schema::table('acesso.menu', function (Blueprint $table) {
            $table->dropForeign(['idsis']);
            $table->dropForeign(['menu_idsis', 'menu_idmen']);
        });
    }
}
