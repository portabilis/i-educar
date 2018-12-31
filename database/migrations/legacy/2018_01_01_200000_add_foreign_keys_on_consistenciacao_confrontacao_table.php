<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoConfrontacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.confrontacao', function (Blueprint $table) {
            $table->foreign(['idins', 'idpes'])
               ->references(['idins', 'idpes'])
               ->on('acesso.pessoa_instituicao');

            $table->foreign('idmet')
               ->references('idmet')
               ->on('consistenciacao.metadado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.confrontacao', function (Blueprint $table) {
            $table->dropForeign(['idins', 'idpes']);
            $table->dropForeign(['idmet']);
        });
    }
}
