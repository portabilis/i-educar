<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarDisciplinaSerieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.disciplina_serie', function (Blueprint $table) {
            $table->foreign('ref_cod_serie')
               ->references('cod_serie')
               ->on('pmieducar.serie')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_disciplina')
               ->references('cod_disciplina')
               ->on('pmieducar.disciplina')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.disciplina_serie', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie']);
            $table->dropForeign(['ref_cod_disciplina']);
        });
    }
}
