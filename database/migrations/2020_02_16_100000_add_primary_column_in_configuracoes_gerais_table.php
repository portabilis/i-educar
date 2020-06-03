<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryColumnInConfiguracoesGeraisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.configuracoes_gerais', function (Blueprint $table) {
            $table->primary('ref_cod_instituicao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.configuracoes_gerais', function (Blueprint $table) {
            $table->dropPrimary('ref_cod_instituicao');
        });
    }
}
