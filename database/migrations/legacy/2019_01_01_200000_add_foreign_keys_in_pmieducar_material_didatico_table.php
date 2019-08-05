<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarMaterialDidaticoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.material_didatico', function (Blueprint $table) {
            $table->foreign('ref_cod_material_tipo')
               ->references('cod_material_tipo')
               ->on('pmieducar.material_tipo')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_instituicao')
               ->references('cod_instituicao')
               ->on('pmieducar.instituicao')
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
        Schema::table('pmieducar.material_didatico', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_material_tipo']);
            $table->dropForeign(['ref_cod_instituicao']);
        });
    }
}
