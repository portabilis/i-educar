<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAlunoHistoricoAlturaPesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.aluno_historico_altura_peso', function (Blueprint $table) {
            $table->foreign('ref_cod_aluno')
                ->references('cod_aluno')
                ->on('pmieducar.aluno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.aluno_historico_altura_peso', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_aluno']);
        });
    }
}
