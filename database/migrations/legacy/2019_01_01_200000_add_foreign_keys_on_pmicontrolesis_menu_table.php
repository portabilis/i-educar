<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmicontrolesisMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmicontrolesis.menu', function (Blueprint $table) {
            $table->foreign('ref_cod_menu_submenu')
               ->references('cod_menu_submenu')
               ->on('portal.menu_submenu')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_menu_pai')
               ->references('cod_menu')
               ->on('pmicontrolesis.menu')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_tutormenu')
               ->references('cod_tutormenu')
               ->on('pmicontrolesis.tutormenu');

            $table->foreign('ref_cod_ico')
               ->references('cod_imagem')
               ->on('portal.imagem')
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
        Schema::table('pmicontrolesis.menu', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_menu_submenu']);
            $table->dropForeign(['ref_cod_menu_pai']);
            $table->dropForeign(['ref_cod_tutormenu']);
            $table->dropForeign(['ref_cod_ico']);
        });
    }
}
