<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnCadastroHistoricoCartaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.historico_cartao', function (Blueprint $table) {
            $table->foreign('idpes_emitiu')
               ->references('idpes')
               ->on('cadastro.pessoa');

            $table->foreign('idpes_cidadao')
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
        Schema::table('cadastro.historico_cartao', function (Blueprint $table) {
            $table->dropForeign(['idpes_emitiu']);
            $table->dropForeign(['idpes_cidadao']);
        });
    }
}
