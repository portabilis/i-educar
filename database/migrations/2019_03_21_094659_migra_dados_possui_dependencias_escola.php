<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigraDadosPossuiDependenciasEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.escola')
            ->whereNotNull('salas_gerais', 'or')
            ->whereNotNull('salas_funcionais', 'or')
            ->whereNotNull('banheiros', 'or')
            ->whereNotNull('laboratorios', 'or')
            ->whereNotNull('salas_atividades', 'or')
            ->whereNotNull('dormitorios', 'or')
            ->whereNotNull('areas_externas', 'or')
            ->update(['possui_dependencias' => 1]);
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
