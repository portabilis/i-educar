<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosEventoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.evento', function (Blueprint $table) {
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
        Schema::table('alimentos.evento', function (Blueprint $table) {
            $table->dropForeign(['idcad']);
        });
    }
}
