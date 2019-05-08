<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalFuncionamentoDiferenciadoToPmieducarTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->smallInteger('local_funcionamento_diferenciado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropColumn('local_funcionamento_diferenciado');
        });
    }
}
