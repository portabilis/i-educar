<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosCardapioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.cardapio', function (Blueprint $table) {
            $table->foreign('idtre')
               ->references('idtre')
               ->on('alimentos.tipo_refeicao');

            $table->foreign('login_inclusao')
               ->references('login')
               ->on('acesso.usuario');

            $table->foreign('idcli')
               ->references('idcli')
               ->on('alimentos.cliente');

            $table->foreign('login_alteracao')
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
        Schema::table('alimentos.cardapio', function (Blueprint $table) {
            $table->dropForeign(['idtre']);
            $table->dropForeign(['login_inclusao']);
            $table->dropForeign(['idcli']);
            $table->dropForeign(['login_alteracao']);
        });
    }
}
