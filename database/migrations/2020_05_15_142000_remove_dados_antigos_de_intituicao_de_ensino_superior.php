<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveDadosAntigosDeIntituicaoDeEnsinoSuperior extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            UPDATE pmieducar.servidor
            SET instituicao_curso_superior_1 = null,
                instituicao_curso_superior_2 = null,
                instituicao_curso_superior_3 = null
            WHERE instituicao_curso_superior_1 IS NOT NULL
            OR instituicao_curso_superior_2 IS NOT NULL
            OR instituicao_curso_superior_3 IS NOT NULL;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
