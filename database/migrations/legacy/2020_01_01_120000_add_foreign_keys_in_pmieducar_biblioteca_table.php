<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarBibliotecaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.biblioteca', function (Blueprint $table) {
            $table->foreign('ref_cod_instituicao')
                ->references('cod_instituicao')
                ->on('pmieducar.instituicao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_escola')
                ->references('cod_escola')
                ->on('pmieducar.escola')
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
        Schema::table('pmieducar.biblioteca', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_instituicao']);
            $table->dropForeign(['ref_cod_escola']);
        });
    }
}
