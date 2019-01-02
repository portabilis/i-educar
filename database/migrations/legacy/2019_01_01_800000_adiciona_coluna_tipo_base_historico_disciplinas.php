<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaTipoBaseHistoricoDisciplinas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.historico_disciplinas', function (Blueprint $table) {
            /** @see ComponenteCurricular_Model_TipoBase::COMUM */
            $table->integer('tipo_base')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.historico_disciplinas', function (Blueprint $table) {
            /** @see ComponenteCurricular_Model_TipoBase::COMUM */
            $table->dropColumn('tipo_base');
        });
    }
}
