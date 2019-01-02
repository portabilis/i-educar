<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPublicSetorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.setor', function (Blueprint $table) {
            $table->foreign('idsetsub')
               ->references('idset')
               ->on('setor')
               ->onDelete('cascade');

            $table->foreign('idsetredir')
               ->references('idset')
               ->on('setor')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.setor', function (Blueprint $table) {
            $table->dropForeign(['idsetsub']);
            $table->dropForeign(['idsetredir']);
        });
    }
}
