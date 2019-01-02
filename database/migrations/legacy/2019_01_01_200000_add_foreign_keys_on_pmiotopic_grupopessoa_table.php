<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiotopicGrupopessoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiotopic.grupopessoa', function (Blueprint $table) {
            $table->foreign(['ref_pessoa_exc', 'ref_grupos_exc'])
               ->references(['ref_ref_cod_pessoa_fj', 'ref_cod_grupos'])
               ->on('pmiotopic.grupomoderador')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign(['ref_pessoa_cad', 'ref_grupos_cad'])
               ->references(['ref_ref_cod_pessoa_fj', 'ref_cod_grupos'])
               ->on('pmiotopic.grupomoderador')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_idpes')
               ->references('idpes')
               ->on('cadastro.fisica')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_grupos')
               ->references('cod_grupos')
               ->on('pmiotopic.grupos')
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
        Schema::table('pmiotopic.grupopessoa', function (Blueprint $table) {
            $table->dropForeign(['ref_pessoa_exc', 'ref_grupos_exc']);
            $table->dropForeign(['ref_pessoa_cad', 'ref_grupos_cad']);
            $table->dropForeign(['ref_idpes']);
            $table->dropForeign(['ref_cod_grupos']);
        });
    }
}
