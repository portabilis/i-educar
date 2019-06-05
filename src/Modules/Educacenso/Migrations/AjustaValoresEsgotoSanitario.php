<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class AjustaValoresEsgotoSanitario implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::statement('UPDATE pmieducar.escola
                                SET esgoto_sanitario = NULL
                              WHERE 2 = ANY (esgoto_sanitario)');
    }
}