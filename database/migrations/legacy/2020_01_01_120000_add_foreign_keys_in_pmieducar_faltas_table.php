<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarFaltasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.faltas', function (Blueprint $table) {
            $table->foreign('ref_cod_matricula')
                ->references('cod_matricula')
                ->on('pmieducar.matricula')
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
        Schema::table('pmieducar.faltas', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_matricula']);
        });
    }
}
