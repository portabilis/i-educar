<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarServidorCursoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.servidor_curso', function (Blueprint $table) {
            $table->foreign('ref_cod_formacao')
                ->references('cod_formacao')
                ->on('pmieducar.servidor_formacao')
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
        Schema::table('pmieducar.servidor_curso', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_formacao']);
        });
    }
}
