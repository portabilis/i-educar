<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmicontrolesisMenuPortalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmicontrolesis.menu_portal', function (Blueprint $table) {
            $table->foreign('ref_funcionario_exc')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario');

            $table->foreign('ref_funcionario_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmicontrolesis.menu_portal', function (Blueprint $table) {
            $table->dropForeign(['ref_funcionario_exc']);
            $table->dropForeign(['ref_funcionario_cad']);
        });
    }
}
