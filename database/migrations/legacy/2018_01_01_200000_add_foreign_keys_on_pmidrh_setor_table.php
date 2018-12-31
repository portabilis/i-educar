<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmidrhSetorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmidrh.setor', function (Blueprint $table) {
            $table->foreign('ref_cod_pessoa_exc')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_pessoa_cad')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_idpes_resp')
               ->references('idpes')
               ->on('cadastro.fisica');

            $table->foreign('ref_cod_setor')
               ->references('cod_setor')
               ->on('pmidrh.setor')
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
        Schema::table('pmidrh.setor', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_pessoa_exc']);
            $table->dropForeign(['ref_cod_pessoa_cad']);
            $table->dropForeign(['ref_idpes_resp']);
            $table->dropForeign(['ref_cod_setor']);
        });
    }
}
