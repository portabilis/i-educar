<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosGuiaRemessaProdutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.guia_remessa_produto', function (Blueprint $table) {
            $table->foreign('idpro')
               ->references('idpro')
               ->on('alimentos.produto');

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
        Schema::table('alimentos.guia_remessa_produto', function (Blueprint $table) {
            $table->dropForeign(['idpro']);
            $table->dropForeign(['idgui']);
        });
    }
}
