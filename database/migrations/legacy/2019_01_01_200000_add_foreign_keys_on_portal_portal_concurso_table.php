<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalPortalConcursoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.portal_concurso', function (Blueprint $table) {
            $table->foreign('ref_ref_cod_pessoa_fj')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
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
        Schema::table('portal.portal_concurso', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_pessoa_fj']);
        });
    }
}
