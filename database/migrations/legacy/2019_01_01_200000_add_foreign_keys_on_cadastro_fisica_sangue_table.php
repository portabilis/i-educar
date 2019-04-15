<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnCadastroFisicaSangueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.fisica_sangue', function (Blueprint $table) {
            $table->foreign('idpes')
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
        Schema::table('cadastro.fisica_sangue', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
        });
    }
}
