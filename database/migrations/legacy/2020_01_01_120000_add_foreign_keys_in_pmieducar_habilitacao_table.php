<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarHabilitacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.habilitacao', function (Blueprint $table) {
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
        Schema::table('pmieducar.habilitacao', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_instituicao']);
        });
    }
}
