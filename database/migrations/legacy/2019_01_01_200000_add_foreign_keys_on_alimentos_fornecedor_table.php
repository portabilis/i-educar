<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosFornecedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.fornecedor', function (Blueprint $table) {
            $table->foreign('idpes')
               ->references('idpes')
               ->on('alimentos.pessoa');

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
        Schema::table('alimentos.fornecedor', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
            $table->dropForeign(['idcli']);
        });
    }
}
