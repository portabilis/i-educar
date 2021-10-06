<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesEducacensoCodEscolaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.educacenso_cod_escola', function (Blueprint $table) {
            $table->foreign('cod_escola')
               ->references('cod_escola')
               ->on('pmieducar.escola')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.educacenso_cod_escola', function (Blueprint $table) {
            $table->dropForeign(['cod_escola']);
        });
    }
}
