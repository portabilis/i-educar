<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class MigraDadosPossuiDependenciasEscola implements EducacensoMigrationInterface
{
    public static function execute()
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
}