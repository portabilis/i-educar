<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAulasPorSemanaToEscolaSerieDisciplina extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->smallInteger('aulas_por_semana')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->dropColumn('aulas_por_semana');
        });
    }
}
