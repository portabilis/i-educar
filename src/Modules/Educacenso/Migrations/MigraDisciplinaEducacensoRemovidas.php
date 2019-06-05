<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class MigraDisciplinaEducacensoRemovidas implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('modules.componente_curricular')
            ->whereIn('codigo_educacenso', [20, 21])
            ->update([
                'codigo_educacenso' => 99
            ]);
    }
}