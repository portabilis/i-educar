<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPublicLogradouroFoneticoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.logradouro_fonetico', function (Blueprint $table) {
            $table->foreign('idlog')
               ->references('idlog')
               ->on('logradouro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.logradouro_fonetico', function (Blueprint $table) {
            $table->dropForeign(['idlog']);
        });
    }
}
