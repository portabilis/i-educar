<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmiotopicGrupomoderadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmiotopic.grupomoderador', function (Blueprint $table) {
            $table->foreign('ref_ref_cod_pessoa_fj')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

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
        Schema::table('pmiotopic.grupomoderador', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_pessoa_fj']);
            $table->dropForeign(['ref_pessoa_exc']);
            $table->dropForeign(['ref_pessoa_cad']);
            $table->dropForeign(['ref_cod_grupos']);
        });
    }
}
