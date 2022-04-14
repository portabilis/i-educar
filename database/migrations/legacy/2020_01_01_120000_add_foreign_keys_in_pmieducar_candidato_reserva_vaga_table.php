<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarCandidatoReservaVagaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.candidato_reserva_vaga', function (Blueprint $table) {
            $table->foreign('ref_cod_turno')
                ->references('id')
                ->on('pmieducar.turma_turno');

            $table->foreign('ref_cod_serie')
                ->references('cod_serie')
                ->on('pmieducar.serie');

            $table->foreign('ref_cod_pessoa_cad')
                ->references('idpes')
                ->on('cadastro.pessoa');

            $table->foreign('ref_cod_aluno')
                ->references('cod_aluno')
                ->on('pmieducar.aluno');

            $table->foreign('ref_cod_escola')
                ->references('cod_escola')
                ->on('pmieducar.escola');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.candidato_reserva_vaga', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_turno']);
            $table->dropForeign(['ref_cod_serie']);
            $table->dropForeign(['ref_cod_pessoa_cad']);
            $table->dropForeign(['ref_cod_aluno']);
            $table->dropForeign(['ref_cod_escola']);
        });
    }
}
