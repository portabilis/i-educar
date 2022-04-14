<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesTransporteAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.transporte_aluno', function (Blueprint $table) {
            $table->foreign('aluno_id')
                ->references('cod_aluno')
                ->on('pmieducar.aluno')
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
        Schema::table('modules.transporte_aluno', function (Blueprint $table) {
            $table->dropForeign(['aluno_id']);
        });
    }
}
