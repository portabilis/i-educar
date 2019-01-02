<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnCadastroSocioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.socio', function (Blueprint $table) {
            $table->foreign('idsis_rev')
               ->references('idsis')
               ->on('acesso.sistema')
               ->onDelete('set null');

            $table->foreign('idsis_cad')
               ->references('idsis')
               ->on('acesso.sistema')
               ->onDelete('set null');

            $table->foreign('idpes_rev')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes_cad')
               ->references('idpes')
               ->on('cadastro.pessoa')
               ->onDelete('set null');

            $table->foreign('idpes_fisica')
               ->references('idpes')
               ->on('cadastro.pessoa');

            $table->foreign('idpes_juridica')
               ->references('idpes')
               ->on('cadastro.juridica');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.socio', function (Blueprint $table) {
            $table->dropForeign(['idsis_rev']);
            $table->dropForeign(['idsis_cad']);
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
            $table->dropForeign(['idpes_fisica']);
            $table->dropForeign(['idpes_juridica']);
        });
    }
}
