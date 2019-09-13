<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class RemoveDeprecatedEtapaEducacensoFromTurmas implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('pmieducar.turma')
            ->where('ano', 2019)
            ->whereIn('etapa_educacenso', [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 65])
            ->update([
                'etapa_educacenso' => null
            ]);
    }
}