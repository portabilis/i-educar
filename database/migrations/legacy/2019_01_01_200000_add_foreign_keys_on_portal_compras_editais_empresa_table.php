<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnPortalComprasEditaisEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal.compras_editais_empresa', function (Blueprint $table) {
            $table->foreign('ref_sigla_uf')
               ->references('sigla_uf')
               ->on('uf')
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
        Schema::table('portal.compras_editais_empresa', function (Blueprint $table) {
            $table->dropForeign(['ref_sigla_uf']);
        });
    }
}
