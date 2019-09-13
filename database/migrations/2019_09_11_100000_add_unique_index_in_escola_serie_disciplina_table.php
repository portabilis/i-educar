<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueIndexInEscolaSerieDisciplinaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->unique(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola_serie_disciplina', function (Blueprint $table) {
            $table->dropUnique(['ref_ref_cod_serie', 'ref_ref_cod_escola', 'ref_cod_disciplina']);
        });
    }
}
