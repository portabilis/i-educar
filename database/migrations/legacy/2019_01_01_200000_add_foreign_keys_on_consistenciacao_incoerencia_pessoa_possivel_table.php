<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoIncoerenciaPessoaPossivelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.incoerencia_pessoa_possivel', function (Blueprint $table) {
            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.pessoa');

            $table->foreign('idinc')
               ->references('idinc')
               ->on('consistenciacao.incoerencia')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.incoerencia_pessoa_possivel', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
            $table->dropForeign(['idinc']);
        });
    }
}
