<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class RemoveValoresInvalidosDaLocalizacaoDiferenciada implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::statement('
            UPDATE pmieducar.escola
            SET localizacao_diferenciada = null
            WHERE localizacao_diferenciada IN (4,5,6);
        ');
    }
}