<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalMenuSubmenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.menu_submenu', function (Blueprint $table) {
            $table->foreign('ref_cod_menu_menu')
               ->references('cod_menu_menu')
               ->on('portal.menu_menu')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal.menu_submenu', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_menu_menu']);
        });
    }
}
