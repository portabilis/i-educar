<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRegistroDiarioIndividualOnQuadroHorario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.quadro_horario_horarios_aux', function (Blueprint $table) {
            $table->boolean('registra_diario_individual')->default(false);
        });

        Schema::table('pmieducar.quadro_horario_horarios', function (Blueprint $table) {
            $table->boolean('registra_diario_individual')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
