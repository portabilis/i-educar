<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class RemoveDadosInvalidosLocalFuncionamentoEscola implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::table('pmieducar.escola')
            ->update(
                [
                    'local_funcionamento' => DB::raw('array_remove(array_remove(array_remove(local_funcionamento, 4), 5), 6)'),
                ]
            );
    }
}