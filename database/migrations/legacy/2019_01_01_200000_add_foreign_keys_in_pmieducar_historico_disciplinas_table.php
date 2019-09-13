<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarHistoricoDisciplinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.historico_disciplinas', function (Blueprint $table) {
            $table->foreign(['ref_ref_cod_aluno', 'ref_sequencial'])
               ->references(['ref_cod_aluno', 'sequencial'])
               ->on('pmieducar.historico_escolar')
               ->onUpdate('cascade')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.historico_disciplinas', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_aluno', 'ref_sequencial']);
        });
    }
}
