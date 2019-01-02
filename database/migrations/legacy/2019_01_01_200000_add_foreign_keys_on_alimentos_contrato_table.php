<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosContratoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.contrato', function (Blueprint $table) {
            $table->foreign('login')
               ->references('login')
               ->on('acesso.usuario');

            $table->foreign('idfor')
               ->references('idfor')
               ->on('alimentos.fornecedor');

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
        Schema::table('alimentos.contrato', function (Blueprint $table) {
            $table->dropForeign(['login']);
            $table->dropForeign(['idfor']);
            $table->dropForeign(['idcli']);
        });
    }
}
