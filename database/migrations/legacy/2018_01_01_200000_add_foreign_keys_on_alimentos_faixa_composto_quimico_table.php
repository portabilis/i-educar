<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosFaixaCompostoQuimicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.faixa_composto_quimico', function (Blueprint $table) {
            $table->foreign('idfae')
               ->references('idfae')
               ->on('alimentos.faixa_etaria');

            $table->foreign('idcom')
               ->references('idcom')
               ->on('alimentos.composto_quimico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.faixa_composto_quimico', function (Blueprint $table) {
            $table->dropForeign(['idfae']);
            $table->dropForeign(['idcom']);
        });
    }
}
