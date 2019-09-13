<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAcervoAcervoAssuntoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.acervo_acervo_assunto', function (Blueprint $table) {
            $table->foreign('ref_cod_acervo')
               ->references('cod_acervo')
               ->on('pmieducar.acervo')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_acervo_assunto')
               ->references('cod_acervo_assunto')
               ->on('pmieducar.acervo_assunto')
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
        Schema::table('pmieducar.acervo_acervo_assunto', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_acervo']);
            $table->dropForeign(['ref_cod_acervo_assunto']);
        });
    }
}
