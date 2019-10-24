<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaParametroOrdenaAlunosPorSequencialEnturmacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->boolean('ordenar_alunos_sequencial_enturmacao')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->dropColumn('ordenar_alunos_sequencial_enturmacao');
        });
    }
}
