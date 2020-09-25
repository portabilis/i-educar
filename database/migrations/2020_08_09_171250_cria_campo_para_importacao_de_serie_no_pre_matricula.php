<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaCampoParaImportacaoDeSerieNoPreMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->addColumn('boolean', 'importar_serie_pre_matricula')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->dropColumn('importar_serie_pre_matricula');
        });
    }
}
