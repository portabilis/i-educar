<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnCadastroEnderecoExternoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.endereco_externo', function (Blueprint $table) {
            $table->foreign('sigla_uf')
               ->references('sigla_uf')
               ->on('uf');

            $table->foreign('idtlog')
               ->references('idtlog')
               ->on('urbano.tipo_logradouro');

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

            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.pessoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.endereco_externo', function (Blueprint $table) {
            $table->dropForeign(['sigla_uf']);
            $table->dropForeign(['idtlog']);
            $table->dropForeign(['idsis_rev']);
            $table->dropForeign(['idsis_cad']);
            $table->dropForeign(['idpes_rev']);
            $table->dropForeign(['idpes_cad']);
            $table->dropForeign(['idpes']);
        });
    }
}
