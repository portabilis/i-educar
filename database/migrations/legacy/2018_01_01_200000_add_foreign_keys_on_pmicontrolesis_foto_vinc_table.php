<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPmicontrolesisFotoVincTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmicontrolesis.foto_vinc', function (Blueprint $table) {
            $table->foreign('ref_cod_foto_evento')
               ->references('cod_foto_evento')
               ->on('pmicontrolesis.foto_evento');

            $table->foreign('ref_cod_acontecimento')
               ->references('cod_acontecimento')
               ->on('pmicontrolesis.acontecimento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmicontrolesis.foto_vinc', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_foto_evento']);
            $table->dropForeign(['ref_cod_acontecimento']);
        });
    }
}
