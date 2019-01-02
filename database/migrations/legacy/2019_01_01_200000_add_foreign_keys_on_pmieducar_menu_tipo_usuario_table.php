<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmieducarMenuTipoUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->foreign('ref_cod_tipo_usuario')
               ->references('cod_tipo_usuario')
               ->on('pmieducar.tipo_usuario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_menu_submenu')
               ->references('cod_menu_submenu')
               ->on('portal.menu_submenu')
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
        Schema::table('pmieducar.menu_tipo_usuario', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_tipo_usuario']);
            $table->dropForeign(['ref_cod_menu_submenu']);
        });
    }
}
