<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInCadastroReligiaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.religiao', function (Blueprint $table) {
            $table->foreign('idpes_exc')
               ->references('idpes')
               ->on('cadastro.fisica')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('idpes_cad')
               ->references('idpes')
               ->on('cadastro.fisica')
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
        Schema::table('cadastro.religiao', function (Blueprint $table) {
            $table->dropForeign(['idpes_exc']);
            $table->dropForeign(['idpes_cad']);
        });
    }
}
