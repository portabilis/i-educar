<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigraDadosTurmaUnificadaEtapaEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.matricula_turma')
            ->whereNotNull('turma_unificada')
            ->where('turma_unificada', '<>', 0)
            ->where('ativo', 1)
            ->update(['etapa_educacenso' => DB::raw('turma_unificada')]);
    }
}
