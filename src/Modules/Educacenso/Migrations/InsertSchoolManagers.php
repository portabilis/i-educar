<?php

namespace iEducar\Modules\Educacenso\Migrations;

use Illuminate\Support\Facades\DB;

class InsertSchoolManagers implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $sql = <<<'SQL'
                INSERT INTO school_managers (employee_id, school_id, role_id, chief)
                (
                    SELECT ref_idpes_gestor,
                           cod_escola,
                           CASE WHEN cargo_gestor NOT IN (1,2) THEN null ELSE cargo_gestor END,
                           true
                      FROM pmieducar.escola
                     WHERE escola.ref_idpes_gestor IS NOT NULL
                )
SQL;
        DB::statement($sql);
    }
}