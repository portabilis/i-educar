<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalNotVincPortalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.not_vinc_portal', function (Blueprint $table) {
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
        Schema::table('portal.not_vinc_portal', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_not_portal']);
        });
    }
}
