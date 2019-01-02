<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnAcessoPessoaInstituicaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acesso.pessoa_instituicao', function (Blueprint $table) {
            $table->foreign('idpes')
               ->references('idpes')
               ->on('cadastro.pessoa');

            $table->foreign('idins')
               ->references('idins')
               ->on('acesso.instituicao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acesso.pessoa_instituicao', function (Blueprint $table) {
            $table->dropForeign(['idpes']);
            $table->dropForeign(['idins']);
        });
    }
}
