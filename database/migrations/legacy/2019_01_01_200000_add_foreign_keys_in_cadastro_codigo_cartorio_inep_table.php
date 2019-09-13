<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInCadastroCodigoCartorioInepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.codigo_cartorio_inep', function (Blueprint $table) {
            $table->foreign('ref_sigla_uf')
               ->references('sigla_uf')
               ->on('uf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.codigo_cartorio_inep', function (Blueprint $table) {
            $table->dropForeign(['ref_sigla_uf']);
        });
    }
}
