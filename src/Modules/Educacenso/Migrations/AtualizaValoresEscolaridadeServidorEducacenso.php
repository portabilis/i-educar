<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class AtualizaValoresEscolaridadeServidorEducacenso implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('cadastro.escolaridade')
            ->whereIn('escolaridade', [3,4])
            ->update([
                'escolaridade' => 7
            ]);
    }
}