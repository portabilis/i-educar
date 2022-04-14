<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.aluno', function (Blueprint $table) {
            $table->foreign('ref_idpes')
                ->references('idpes')
                ->on('cadastro.fisica')
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
        Schema::table('pmieducar.aluno', function (Blueprint $table) {
            $table->dropForeign(['ref_idpes']);
        });
    }
}
