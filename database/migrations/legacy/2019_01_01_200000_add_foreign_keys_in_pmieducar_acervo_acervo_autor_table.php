<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarAcervoAcervoAutorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.acervo_acervo_autor', function (Blueprint $table) {
            $table->foreign('ref_cod_acervo')
               ->references('cod_acervo')
               ->on('pmieducar.acervo')
               ->onUpdate('restrict')
               ->onDelete('restrict');

            $table->foreign('ref_cod_acervo_autor')
               ->references('cod_acervo_autor')
               ->on('pmieducar.acervo_autor')
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
        Schema::table('pmieducar.acervo_acervo_autor', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_acervo']);
            $table->dropForeign(['ref_cod_acervo_autor']);
        });
    }
}
