<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosGrupoQuimicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.grupo_quimico', function (Blueprint $table) {
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
        Schema::table('alimentos.grupo_quimico', function (Blueprint $table) {
            $table->dropForeign(['idcli']);
        });
    }
}
