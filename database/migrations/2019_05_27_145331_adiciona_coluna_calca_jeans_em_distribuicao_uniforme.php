<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaCalcaJeansEmDistribuicaoUniforme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->integer('calca_jeans_qtd')->nullable();
            $table->string('calca_jeans_tm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->dropColumn('calca_jeans_qtd');
            $table->dropColumn('calca_jeans_tm');
        });
    }
}
