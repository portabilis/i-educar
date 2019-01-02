<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoOperacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.operacao', function (Blueprint $table) {
            $table->foreign('idsis')
               ->references('idsis')
               ->on('acesso.sistema');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.operacao', function (Blueprint $table) {
            $table->dropForeign(['idsis']);
        });
    }
}
