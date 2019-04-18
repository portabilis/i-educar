<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaCampoAguaPotavelConsumoEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->integer('agua_potavel_consumo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('agua_potavel_consumo');
        });
    }
}
