<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalJorArquivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.jor_arquivo', function (Blueprint $table) {
            $table->foreign('ref_cod_jor_edicao')
               ->references('cod_jor_edicao')
               ->on('portal.jor_edicao')
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
        Schema::table('portal.jor_arquivo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_jor_edicao']);
        });
    }
}
