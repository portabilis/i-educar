<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesRegraAvaliacaoSerieAnoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao_serie_ano', function (Blueprint $table) {
            $table->foreign('serie_id')
               ->references('cod_serie')
               ->on('pmieducar.serie')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('regra_avaliacao_id')
               ->references('id')
               ->on('modules.regra_avaliacao')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('regra_avaliacao_diferenciada_id')
               ->references('id')
               ->on('modules.regra_avaliacao')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.regra_avaliacao_serie_ano', function (Blueprint $table) {
            $table->dropForeign(['serie_id']);
            $table->dropForeign(['regra_avaliacao_id']);
            $table->dropForeign(['regra_avaliacao_diferenciada_id']);
        });
    }
}
