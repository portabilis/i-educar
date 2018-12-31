<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalMaillingGrupoEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.mailling_grupo_email', function (Blueprint $table) {
            $table->foreign('ref_cod_mailling_grupo')
               ->references('cod_mailling_grupo')
               ->on('portal.mailling_grupo')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_mailling_email')
               ->references('cod_mailling_email')
               ->on('portal.mailling_email')
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
        Schema::table('portal.mailling_grupo_email', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_mailling_grupo']);
            $table->dropForeign(['ref_cod_mailling_email']);
        });
    }
}
