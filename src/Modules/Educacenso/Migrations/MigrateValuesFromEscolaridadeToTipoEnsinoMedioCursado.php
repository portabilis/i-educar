<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursado implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::statement('
            UPDATE pmieducar.servidor
            set tipo_ensino_medio_cursado = CASE WHEN escolaridade.escolaridade = 3 then 2 else 4 end
            from cadastro.escolaridade
            where servidor.ref_idesco = escolaridade.idesco
            and escolaridade.escolaridade IN (3,4)

        ');
    }
}