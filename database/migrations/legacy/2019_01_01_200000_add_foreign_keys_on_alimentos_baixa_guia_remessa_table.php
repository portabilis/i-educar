<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosBaixaGuiaRemessaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.baixa_guia_remessa', function (Blueprint $table) {
            $table->foreign('login_baixa')
               ->references('login')
               ->on('acesso.usuario');

            $table->foreign('idgui')
               ->references('idgui')
               ->on('alimentos.guia_remessa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.baixa_guia_remessa', function (Blueprint $table) {
            $table->dropForeign(['login_baixa']);
            $table->dropForeign(['idgui']);
        });
    }
}
