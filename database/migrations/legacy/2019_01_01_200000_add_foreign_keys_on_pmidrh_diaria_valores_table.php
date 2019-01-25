<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmidrhDiariaValoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmidrh.diaria_valores', function (Blueprint $table) {
            $table->foreign('ref_funcionario_cadastro')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_diaria_grupo')
               ->references('cod_diaria_grupo')
               ->on('pmidrh.diaria_grupo')
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
        Schema::table('pmidrh.diaria_valores', function (Blueprint $table) {
            $table->dropForeign(['ref_funcionario_cadastro']);
            $table->dropForeign(['ref_cod_diaria_grupo']);
        });
    }
}
