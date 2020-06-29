<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class UpdateEducacensoInstitutionToLayout2020 implements EducacensoMigrationInterface
{

    public static function execute()
    {
        DB::unprepared(file_get_contents(database_path('sqls/educacenso/2020_instituicoes_ensino.sql')));
    }
}
