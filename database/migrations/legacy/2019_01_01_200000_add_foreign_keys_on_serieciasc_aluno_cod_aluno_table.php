<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnSerieciascAlunoCodAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('serieciasc.aluno_cod_aluno', function (Blueprint $table) {
            $table->foreign('cod_aluno')
               ->references('cod_aluno')
               ->on('pmieducar.aluno')
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
        Schema::table('serieciasc.aluno_cod_aluno', function (Blueprint $table) {
            $table->dropForeign(['cod_aluno']);
        });
    }
}
