<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyInMenuTipoUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->foreign('menu_id')->on('menus')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->dropForeign('menu_id');
        });
    }
}
