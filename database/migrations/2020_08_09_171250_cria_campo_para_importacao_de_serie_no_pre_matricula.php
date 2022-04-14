<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CriaCampoParaImportacaoDeSerieNoPreMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $result = DB::selectOne('SELECT column_name
                    FROM information_schema.columns
                    WHERE table_name=\'serie\' and column_name=\'importar_serie_pre_matricula\';');

        if (!$result) {
            Schema::table('pmieducar.serie', function (Blueprint $table) {
                $table->addColumn('boolean', 'importar_serie_pre_matricula')->default(false);
            });
        }
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
