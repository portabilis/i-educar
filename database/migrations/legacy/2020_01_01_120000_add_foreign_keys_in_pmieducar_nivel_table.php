<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarNivelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.nivel', function (Blueprint $table) {
            $table->foreign('ref_cod_nivel_anterior')
                ->references('cod_nivel')
                ->on('pmieducar.nivel')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_categoria_nivel')
                ->references('cod_categoria_nivel')
                ->on('pmieducar.categoria_nivel')
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
        Schema::table('pmieducar.nivel', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_nivel_anterior']);
            $table->dropForeign(['ref_cod_categoria_nivel']);
        });
    }
}
