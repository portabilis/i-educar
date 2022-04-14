<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAnoLetivoModuloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.ano_letivo_modulo', function (Blueprint $table) {
            $table->foreign(['ref_ref_cod_escola', 'ref_ano'])
                ->references(['ref_cod_escola', 'ano'])
                ->on('pmieducar.escola_ano_letivo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_modulo')
                ->references('cod_modulo')
                ->on('pmieducar.modulo')
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
        Schema::table('pmieducar.ano_letivo_modulo', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_escola', 'ref_ano']);
            $table->dropForeign(['ref_cod_modulo']);
        });
    }
}
