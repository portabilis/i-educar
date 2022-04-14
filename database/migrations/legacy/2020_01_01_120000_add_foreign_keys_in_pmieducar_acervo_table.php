<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAcervoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.acervo', function (Blueprint $table) {
            $table->foreign('ref_cod_exemplar_tipo')
                ->references('cod_exemplar_tipo')
                ->on('pmieducar.exemplar_tipo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_biblioteca')
                ->references('cod_biblioteca')
                ->on('pmieducar.biblioteca')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_acervo_idioma')
                ->references('cod_acervo_idioma')
                ->on('pmieducar.acervo_idioma')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_acervo')
                ->references('cod_acervo')
                ->on('pmieducar.acervo')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_acervo_editora')
                ->references('cod_acervo_editora')
                ->on('pmieducar.acervo_editora')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_acervo_colecao')
                ->references('cod_acervo_colecao')
                ->on('pmieducar.acervo_colecao')
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
        Schema::table('pmieducar.acervo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_exemplar_tipo']);
            $table->dropForeign(['ref_cod_biblioteca']);
            $table->dropForeign(['ref_cod_acervo_idioma']);
            $table->dropForeign(['ref_cod_acervo']);
            $table->dropForeign(['ref_cod_acervo_editora']);
            $table->dropForeign(['ref_cod_acervo_colecao']);
        });
    }
}
