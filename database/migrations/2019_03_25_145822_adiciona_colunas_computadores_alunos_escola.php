<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunasComputadoresAlunosEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->integer('quantidade_computadores_alunos_mesa')->nullable();
            $table->integer('quantidade_computadores_alunos_portateis')->nullable();
            $table->integer('quantidade_computadores_alunos_tablets')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('quantidade_computadores_alunos_mesa');
            $table->dropColumn('quantidade_computadores_alunos_portateis');
            $table->dropColumn('quantidade_computadores_alunos_tablets');
        });
    }
}
