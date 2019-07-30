<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPublicLogradouroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.logradouro', function (Blueprint $table) {
            $table->foreign('idtlog')
               ->references('idtlog')
               ->on('urbano.tipo_logradouro');

            $table->foreign('idpes_rev')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes_cad')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.logradouro', function (Blueprint $table) {
            $table->dropForeign(['idtlog']);
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
        });
    }
}
