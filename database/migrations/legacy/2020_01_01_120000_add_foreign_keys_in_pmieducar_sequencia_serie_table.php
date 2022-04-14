<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarSequenciaSerieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.sequencia_serie', function (Blueprint $table) {
            $table->foreign('ref_serie_origem')
                ->references('cod_serie')
                ->on('pmieducar.serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_serie_destino')
                ->references('cod_serie')
                ->on('pmieducar.serie')
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
        Schema::table('pmieducar.sequencia_serie', function (Blueprint $table) {
            $table->dropForeign(['ref_serie_origem']);
            $table->dropForeign(['ref_serie_destino']);
        });
    }
}
