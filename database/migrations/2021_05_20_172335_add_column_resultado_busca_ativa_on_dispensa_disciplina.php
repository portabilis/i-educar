<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnResultadoBuscaAtivaOnDispensaDisciplina extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispensa_disciplina', function (Blueprint $table) {
            $table->smallInteger('resultado_busca_ativa')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispensa_disciplina', function (Blueprint $table) {
            $table->dropColumn('resultado_busca_ativa');
        });
    }
}
