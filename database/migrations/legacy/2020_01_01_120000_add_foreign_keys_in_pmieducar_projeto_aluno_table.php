<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarProjetoAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.projeto_aluno', function (Blueprint $table) {
            $table->foreign('ref_cod_projeto')
                ->references('cod_projeto')
                ->on('pmieducar.projeto');

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
        Schema::table('pmieducar.projeto_aluno', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_projeto']);
            $table->dropForeign(['ref_cod_aluno']);
        });
    }
}
