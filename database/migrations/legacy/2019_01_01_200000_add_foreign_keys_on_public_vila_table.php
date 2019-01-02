<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPublicVilaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.vila', function (Blueprint $table) {
            $table->foreign('idmun')
               ->references('idmun')
               ->on('municipio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.vila', function (Blueprint $table) {
            $table->dropForeign(['idmun']);
        });
    }
}
