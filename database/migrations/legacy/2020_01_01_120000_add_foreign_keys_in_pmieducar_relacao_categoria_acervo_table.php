<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarRelacaoCategoriaAcervoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.relacao_categoria_acervo', function (Blueprint $table) {
            $table->foreign('ref_cod_acervo')
                ->references('cod_acervo')
                ->on('pmieducar.acervo');

            $table->foreign('categoria_id')
                ->references('id')
                ->on('pmieducar.categoria_obra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.relacao_categoria_acervo', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_acervo']);
            $table->dropForeign(['categoria_id']);
        });
    }
}
