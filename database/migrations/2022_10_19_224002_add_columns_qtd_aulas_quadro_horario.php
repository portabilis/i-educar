<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsQtdAulasQuadroHorario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.quadro_horario_horarios_aux', function (Blueprint $table) {
            $table->integer('qtd_aulas')->nullable();
        });

        Schema::table('pmieducar.quadro_horario_horarios', function (Blueprint $table) {
            $table->integer('qtd_aulas')->nullable();
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
