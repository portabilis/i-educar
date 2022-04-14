<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarFonteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.fonte', function (Blueprint $table) {
            $table->foreign('ref_cod_biblioteca')
                ->references('cod_biblioteca')
                ->on('pmieducar.biblioteca')
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
        Schema::table('pmieducar.fonte', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_biblioteca']);
        });
    }
}
