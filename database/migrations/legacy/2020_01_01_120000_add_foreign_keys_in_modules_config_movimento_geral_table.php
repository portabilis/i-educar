<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesConfigMovimentoGeralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.config_movimento_geral', function (Blueprint $table) {
            $table->foreign('ref_cod_serie')
               ->references('cod_serie')
               ->on('pmieducar.serie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.config_movimento_geral', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_serie']);
        });
    }
}
