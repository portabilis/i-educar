<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaEmiteAtoAutorizativo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.configuracoes_gerais', function (Blueprint $table) {
            $table->boolean('emitir_ato_autorizativo')->default(false);
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
            $table->dropColumn('emitir_ato_autorizativo');
        });
    }
}
