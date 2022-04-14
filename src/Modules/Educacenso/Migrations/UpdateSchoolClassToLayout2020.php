<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class UpdateSchoolClassToLayout2020 implements EducacensoMigrationInterface
{
    public static function execute()
    {
        DB::unprepared('UPDATE pmieducar.turma
                              SET atividades_complementares = array_remove(atividades_complementares, 13108)
                              WHERE ARRAY[13108] <@ atividades_complementares; ');
    }
}
