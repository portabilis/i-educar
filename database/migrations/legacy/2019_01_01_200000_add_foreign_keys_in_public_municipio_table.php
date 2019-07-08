<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPublicMunicipioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.municipio', function (Blueprint $table) {
            $table->foreign('sigla_uf')
               ->references('sigla_uf')
               ->on('uf');

            $table->foreign('idpes_rev')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes_cad')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idmun_pai')
               ->references('idmun')
               ->on('municipio');

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
        Schema::table('public.municipio', function (Blueprint $table) {
            $table->dropForeign(['sigla_uf']);
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
            $table->dropForeign(['idmun_pai']);
            $table->dropForeign(['idmun']);
        });
    }
}
