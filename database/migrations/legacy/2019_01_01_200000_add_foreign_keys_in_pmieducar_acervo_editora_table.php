<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAcervoEditoraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.acervo_editora', function (Blueprint $table) {
            $table->foreign('ref_sigla_uf')
               ->references('sigla_uf')
               ->on('uf')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_idtlog')
               ->references('idtlog')
               ->on('urbano.tipo_logradouro')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_biblioteca')
               ->references('cod_biblioteca')
               ->on('pmieducar.biblioteca')
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
        Schema::table('pmieducar.acervo_editora', function (Blueprint $table) {
            $table->dropForeign(['ref_sigla_uf']);
            $table->dropForeign(['ref_idtlog']);
            $table->dropForeign(['ref_cod_biblioteca']);
        });
    }
}
