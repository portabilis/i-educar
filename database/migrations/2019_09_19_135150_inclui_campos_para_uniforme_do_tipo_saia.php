<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncluiCamposParaUniformeDoTipoSaia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->smallInteger('saia_qtd')->nullable();
        });
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->string('saia_tm', 20)->nullable();
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
            $table->dropColumn('saia_qtd');
        });
        Schema::table('pmieducar.distribuicao_uniforme', function (Blueprint $table) {
            $table->dropColumn('saia_tm');
        });
    }
}
