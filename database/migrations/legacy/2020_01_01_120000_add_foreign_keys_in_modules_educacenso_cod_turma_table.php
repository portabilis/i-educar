<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesEducacensoCodTurmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->foreign('cod_turma')
                ->references('cod_turma')
                ->on('pmieducar.turma')
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
        Schema::table('modules.educacenso_cod_turma', function (Blueprint $table) {
            $table->dropForeign(['cod_turma']);
        });
    }
}
