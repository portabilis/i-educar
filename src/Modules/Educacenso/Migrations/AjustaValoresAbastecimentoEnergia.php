<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class AjustaValoresAbastecimentoEnergia implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::statement('UPDATE pmieducar.escola
                                SET abastecimento_energia = NULL
                              WHERE 2 = ANY (abastecimento_energia)
                                 OR 3 = ANY (abastecimento_energia)');
    }
}