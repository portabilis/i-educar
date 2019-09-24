<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaParametroPermitirMatriculaForaPeriodoLetivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->boolean('permitir_matricula_fora_periodo_letivo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->dropColumn('permitir_matricula_fora_periodo_letivo');
        });
    }
}
