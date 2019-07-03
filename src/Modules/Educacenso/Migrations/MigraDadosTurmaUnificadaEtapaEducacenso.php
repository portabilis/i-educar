<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class MigraDadosTurmaUnificadaEtapaEducacenso implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('pmieducar.matricula_turma')
            ->whereNotNull('turma_unificada')
            ->where('turma_unificada', '<>', 0)
            ->where('ativo', 1)
            ->update(['etapa_educacenso' => DB::raw('turma_unificada')]);
    }
}