<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarSeriePreRequisitoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.serie_pre_requisito', function (Blueprint $table) {
            $table->foreign('ref_cod_serie')
                ->references('cod_serie')
                ->on('pmieducar.serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_pre_requisito')
                ->references('cod_pre_requisito')
                ->on('pmieducar.pre_requisito')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_operador')
                ->references('cod_operador')
                ->on('pmieducar.operador')
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
        Schema::table('pmieducar.serie_pre_requisito', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie']);
            $table->dropForeign(['ref_cod_pre_requisito']);
            $table->dropForeign(['ref_cod_operador']);
        });
    }
}
