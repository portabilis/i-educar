<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaHistoricoDoTipoJsonNaTabelaCandidatoReservaDeVagas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.candidato_reserva_vaga', function (Blueprint $table) {
            $table->json('historico')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.candidato_reserva_vaga', function (Blueprint $table) {
            $table->dropColumn('historico');
        });
    }
}
