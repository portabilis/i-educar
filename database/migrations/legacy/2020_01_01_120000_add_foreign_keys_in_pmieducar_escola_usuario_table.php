<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarEscolaUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola_usuario', function (Blueprint $table) {
            $table->foreign('ref_cod_usuario')
               ->references('cod_usuario')
               ->on('pmieducar.usuario');

            $table->foreign('ref_cod_escola')
               ->references('cod_escola')
               ->on('pmieducar.escola');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola_usuario', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_usuario']);
            $table->dropForeign(['ref_cod_escola']);
        });
    }
}
