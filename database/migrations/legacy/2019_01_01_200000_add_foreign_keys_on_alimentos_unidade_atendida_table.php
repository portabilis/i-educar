<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosUnidadeAtendidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.unidade_atendida', function (Blueprint $table) {
            $table->foreign('idtip')
               ->references('idtip')
               ->on('alimentos.tipo_unidade');

            $table->foreign('idpes')
               ->references('idpes')
               ->on('alimentos.pessoa');

            $table->foreign('idcli')
               ->references('idcli')
               ->on('alimentos.cliente');

            $table->foreign('idcad')
               ->references('idcad')
               ->on('alimentos.calendario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.unidade_atendida', function (Blueprint $table) {
            $table->dropForeign(['idtip']);
            $table->dropForeign(['idpes']);
            $table->dropForeign(['idcli']);
            $table->dropForeign(['idcad']);
        });
    }
}
