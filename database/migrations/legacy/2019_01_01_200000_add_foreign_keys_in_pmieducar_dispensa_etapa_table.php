<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarDispensaEtapaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.dispensa_etapa', function (Blueprint $table) {
            $table->foreign('ref_cod_dispensa')
               ->references('cod_dispensa')
               ->on('pmieducar.dispensa_disciplina');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.dispensa_etapa', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_dispensa']);
        });
    }
}
