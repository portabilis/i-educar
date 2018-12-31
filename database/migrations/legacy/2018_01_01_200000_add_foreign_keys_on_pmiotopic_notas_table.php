<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiotopicNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiotopic.notas', function (Blueprint $table) {
            $table->foreign('ref_pessoa_exc')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_pessoa_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

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
        Schema::table('pmiotopic.notas', function (Blueprint $table) {
            $table->dropForeign(['ref_pessoa_exc']);
            $table->dropForeign(['ref_pessoa_cad']);
            $table->dropForeign(['ref_idpes']);
        });
    }
}
