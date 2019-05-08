<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertSchoolManagers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<'SQL'
                INSERT INTO school_managers (individual_id, school_id, role_id, chief)
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
