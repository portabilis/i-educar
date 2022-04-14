<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColunasNaoUtilizadasDaTabelaMoradiaAluno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.moradia_aluno', function (Blueprint $table) {
            $table->dropColumn('celular');
        });

        Schema::table('modules.moradia_aluno', function (Blueprint $table) {
            $table->dropColumn('computador');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.moradia_aluno', function (Blueprint $table) {
            $table->char('celular', 1)->nullable();
        });
        Schema::table('modules.moradia_aluno', function (Blueprint $table) {
            $table->char('computador', 1)->nullable();
        });
    }
}
