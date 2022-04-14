<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesRegraAvaliacaoRecuperacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao_recuperacao', function (Blueprint $table) {
            $table->foreign('regra_avaliacao_id')
                ->references('id')
                ->on('modules.regra_avaliacao')
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
        Schema::table('modules.regra_avaliacao_recuperacao', function (Blueprint $table) {
            $table->dropForeign(['regra_avaliacao_id']);
        });
    }
}
