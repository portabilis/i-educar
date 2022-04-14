<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAlunoAlunoBeneficioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.aluno_aluno_beneficio', function (Blueprint $table) {
            $table->foreign('aluno_id')
                ->references('cod_aluno')
                ->on('pmieducar.aluno');

            $table->foreign('aluno_beneficio_id')
                ->references('cod_aluno_beneficio')
                ->on('pmieducar.aluno_beneficio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.aluno_aluno_beneficio', function (Blueprint $table) {
            $table->dropForeign(['aluno_id']);
            $table->dropForeign(['aluno_beneficio_id']);
        });
    }
}
