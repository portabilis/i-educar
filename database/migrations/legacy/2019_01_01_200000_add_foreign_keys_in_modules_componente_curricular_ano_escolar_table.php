<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesComponenteCurricularAnoEscolarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.componente_curricular_ano_escolar', function (Blueprint $table) {
            $table->foreign('componente_curricular_id')
               ->references('id')
               ->on('modules.componente_curricular')
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
        Schema::table('modules.componente_curricular_ano_escolar', function (Blueprint $table) {
            $table->dropForeign(['componente_curricular_id']);
        });
    }
}
