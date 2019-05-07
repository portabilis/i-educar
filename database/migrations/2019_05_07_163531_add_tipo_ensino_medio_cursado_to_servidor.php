<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoEnsinoMedioCursadoToServidor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->integer('tipo_ensino_medio_cursado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->dropColumn('tipo_ensino_medio_cursado');
        });
    }
}
