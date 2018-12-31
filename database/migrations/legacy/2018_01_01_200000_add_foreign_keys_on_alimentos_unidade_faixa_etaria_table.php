<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosUnidadeFaixaEtariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.unidade_faixa_etaria', function (Blueprint $table) {
            $table->foreign('iduni')
               ->references('iduni')
               ->on('alimentos.unidade_atendida');

            $table->foreign('idfae')
               ->references('idfae')
               ->on('alimentos.faixa_etaria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.unidade_faixa_etaria', function (Blueprint $table) {
            $table->dropForeign(['iduni']);
            $table->dropForeign(['idfae']);
        });
    }
}
