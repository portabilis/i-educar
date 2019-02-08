<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnConsistenciacaoIncoerenciaDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consistenciacao.incoerencia_documento', function (Blueprint $table) {
            $table->foreign('idinc')
               ->references('idinc')
               ->on('consistenciacao.incoerencia')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consistenciacao.incoerencia_documento', function (Blueprint $table) {
            $table->dropForeign(['idinc']);
        });
    }
}
