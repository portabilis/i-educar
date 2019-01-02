<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosCompostoQuimicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.composto_quimico', function (Blueprint $table) {
            $table->foreign('idgrpq')
               ->references('idgrpq')
               ->on('alimentos.grupo_quimico');

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
        Schema::table('alimentos.composto_quimico', function (Blueprint $table) {
            $table->dropForeign(['idgrpq']);
            $table->dropForeign(['idcli']);
        });
    }
}
