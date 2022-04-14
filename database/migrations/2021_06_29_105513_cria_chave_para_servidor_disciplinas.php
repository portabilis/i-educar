<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CriaChaveParaServidorDisciplinas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.servidor_disciplina', function (Blueprint $table) {
            $table->foreign('ref_cod_funcao')->on('pmieducar.servidor_funcao')->references('cod_servidor_funcao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.servidor_disciplina', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_funcao']);
        });
    }
}
