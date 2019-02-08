<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlteraTamanhoColunaCartorioNaTabelaHistoricoDocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico.documento', function (Blueprint $table) {
            $table->string('cartorio_cert_civil')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico.documento', function (Blueprint $table) {
            $table->string('cartorio_cert_civil', 150)->change();
        });
    }
}
