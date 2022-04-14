<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarEscolaSerieDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->foreign(['ref_ref_cod_escola', 'ref_ref_cod_serie'])
                ->references(['ref_cod_escola', 'ref_cod_serie'])
                ->on('pmieducar.escola_serie')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_disciplina')
                ->references('id')
                ->on('modules.componente_curricular')
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
        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_escola', 'ref_ref_cod_serie']);
            $table->dropForeign(['ref_cod_disciplina']);
        });
    }
}
