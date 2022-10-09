<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsProfessorSubstitutoQuadroHorario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.quadro_horario_horarios_aux', function (Blueprint $table) {
            $table->integer('ref_cod_servidor_substituto_1')->nullable();
            $table->integer('ref_cod_servidor_substituto_2')->nullable();
        });

        Schema::table('pmieducar.quadro_horario_horarios', function (Blueprint $table) {
            $table->integer('ref_cod_servidor_substituto_1')->nullable();
            $table->integer('ref_cod_servidor_substituto_2')->nullable();
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
