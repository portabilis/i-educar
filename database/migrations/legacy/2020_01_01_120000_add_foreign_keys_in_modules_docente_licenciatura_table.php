<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesDocenteLicenciaturaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.docente_licenciatura', function (Blueprint $table) {
            $table->foreign('ies_id')
               ->references('id')
               ->on('modules.educacenso_ies')
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
        Schema::table('modules.docente_licenciatura', function (Blueprint $table) {
            $table->dropForeign(['ies_id']);
        });
    }
}
