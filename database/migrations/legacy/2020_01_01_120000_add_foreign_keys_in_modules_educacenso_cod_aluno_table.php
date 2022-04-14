<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesEducacensoCodAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.educacenso_cod_aluno', function (Blueprint $table) {
            $table->foreign('cod_aluno')
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
        Schema::table('modules.educacenso_cod_aluno', function (Blueprint $table) {
            $table->dropForeign(['cod_aluno']);
        });
    }
}
