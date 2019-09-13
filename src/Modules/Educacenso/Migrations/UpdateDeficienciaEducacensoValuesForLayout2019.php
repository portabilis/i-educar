<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class UpdateDeficienciaEducacensoValuesForLayout2019 implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('cadastro.deficiencia')
            ->whereIn('deficiencia_educacenso', [9, 10, 11, 12])
            ->update(['deficiencia_educacenso' => null]);
    }
}