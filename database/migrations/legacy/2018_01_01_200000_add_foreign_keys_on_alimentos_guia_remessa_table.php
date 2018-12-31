<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosGuiaRemessaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.guia_remessa', function (Blueprint $table) {
            $table->foreign('iduni')
               ->references('iduni')
               ->on('alimentos.unidade_atendida');

            $table->foreign('idfor')
               ->references('idfor')
               ->on('alimentos.fornecedor');

            $table->foreign('login_emissao')
               ->references('login')
               ->on('acesso.usuario');

            $table->foreign('idcon')
               ->references('idcon')
               ->on('alimentos.contrato');

            $table->foreign('idcli')
               ->references('idcli')
               ->on('alimentos.cliente');

            $table->foreign('login_cancelamento')
               ->references('login')
               ->on('acesso.usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.guia_remessa', function (Blueprint $table) {
            $table->dropForeign(['iduni']);
            $table->dropForeign(['idfor']);
            $table->dropForeign(['login_emissao']);
            $table->dropForeign(['idcon']);
            $table->dropForeign(['idcli']);
            $table->dropForeign(['login_cancelamento']);
        });
    }
}
