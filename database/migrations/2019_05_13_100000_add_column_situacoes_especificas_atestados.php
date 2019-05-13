<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSituacoesEspecificasAtestados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.configuracoes_gerais', function (Blueprint $table) {
            $table->boolean('situacoes_especificas_atestados')->default(false);
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
            $table->dropColumn('situacoes_especificas_atestados');
        });
    }
}
