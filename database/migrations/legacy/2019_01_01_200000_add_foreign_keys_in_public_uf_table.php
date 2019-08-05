<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPublicUfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.uf', function (Blueprint $table) {
            $table->foreign('idpais')
               ->references('idpais')
               ->on('pais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.uf', function (Blueprint $table) {
            $table->dropForeign(['idpais']);
        });
    }
}
