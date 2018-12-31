<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalMaillingHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.mailling_historico', function (Blueprint $table) {
            $table->foreign('ref_ref_cod_pessoa_fj')
               ->references('ref_cod_pessoa_fj')
               ->on('portal.funcionario')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_mailling_grupo')
               ->references('cod_mailling_grupo')
               ->on('portal.mailling_grupo')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_not_portal')
               ->references('cod_not_portal')
               ->on('portal.not_portal')
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
        Schema::table('portal.mailling_historico', function (Blueprint $table) {
            $table->dropForeign(['ref_ref_cod_pessoa_fj']);
            $table->dropForeign(['ref_cod_mailling_grupo']);
            $table->dropForeign(['ref_cod_not_portal']);
        });
    }
}
