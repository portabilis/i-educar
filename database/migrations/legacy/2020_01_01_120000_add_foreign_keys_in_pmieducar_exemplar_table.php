<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarExemplarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.exemplar', function (Blueprint $table) {
            $table->foreign('ref_cod_situacao')
                ->references('cod_situacao')
                ->on('pmieducar.situacao')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_motivo_baixa')
                ->references('cod_motivo_baixa')
                ->on('pmieducar.motivo_baixa')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_fonte')
                ->references('cod_fonte')
                ->on('pmieducar.fonte')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('ref_cod_acervo')
                ->references('cod_acervo')
                ->on('pmieducar.acervo')
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
        Schema::table('pmieducar.exemplar', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_situacao']);
            $table->dropForeign(['ref_cod_motivo_baixa']);
            $table->dropForeign(['ref_cod_fonte']);
            $table->dropForeign(['ref_cod_acervo']);
        });
    }
}
