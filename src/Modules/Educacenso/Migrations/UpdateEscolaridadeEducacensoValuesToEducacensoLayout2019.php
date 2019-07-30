<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019 implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('cadastro.escolaridade')
            ->where('escolaridade', 5)
            ->update([
                'escolaridade' => 7
            ]);
    }
}