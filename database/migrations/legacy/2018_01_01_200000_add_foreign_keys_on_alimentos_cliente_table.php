<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAlimentosClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alimentos.cliente', function (Blueprint $table) {
            $table->foreign('idpes')
               ->references('idpes')
               ->on('alimentos.pessoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alimentos.cliente', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
        });
    }
}
