<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInModulesRegraAvaliacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->foreign('regra_diferenciada_id')
                ->references('id')
                ->on('modules.regra_avaliacao');

            $table->foreign(['tabela_arredondamento_id', 'instituicao_id'])
                ->references(['id', 'instituicao_id'])
                ->on('modules.tabela_arredondamento')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign(['formula_recuperacao_id', 'instituicao_id'])
                ->references(['id', 'instituicao_id'])
                ->on('modules.formula_media')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign(['formula_media_id', 'instituicao_id'])
                ->references(['id', 'instituicao_id'])
                ->on('modules.formula_media')
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
        Schema::table('modules.regra_avaliacao', function (Blueprint $table) {
            $table->dropForeign(['regra_diferenciada_id']);
            $table->dropForeign(['tabela_arredondamento_id', 'instituicao_id']);
            $table->dropForeign(['formula_recuperacao_id', 'instituicao_id']);
            $table->dropForeign(['formula_media_id', 'instituicao_id']);
        });
    }
}
