<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarTipoAvaliacaoValoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.tipo_avaliacao_valores', function (Blueprint $table) {
            $table->foreign('ref_cod_tipo_avaliacao')
               ->references('cod_tipo_avaliacao')
               ->on('pmieducar.tipo_avaliacao')
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
        Schema::table('pmieducar.tipo_avaliacao_valores', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_tipo_avaliacao']);
        });
    }
}
