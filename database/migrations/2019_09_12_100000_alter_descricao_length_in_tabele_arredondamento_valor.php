<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDescricaoLengthInTabeleArredondamentoValor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.tabela_arredondamento_valor', function (Blueprint $table) {
            $table->string('descricao', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.tabela_arredondamento_valor', function (Blueprint $table) {
            $table->string('descricao', 25)->change();
        });
    }
}
