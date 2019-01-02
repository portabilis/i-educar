<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosMedidasCaseirasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.medidas_caseiras', function (Blueprint $table) {
            $table->foreign('idcli')
               ->references('idcli')
               ->on('alimentos.cliente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.medidas_caseiras', function (Blueprint $table) {
            $table->dropForeign(['idcli']);
        });
    }
}
