<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CorrigeGestoresNasEscolas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<'sql'
            UPDATE pmieducar.escola
            SET ref_idpes_gestor = aux.employee_id
            FROM (
                     SELECT cod_escola, school_managers.employee_id
                     FROM pmieducar.escola
                     JOIN school_managers ON school_managers.school_id = escola.cod_escola
                     JOIN cadastro.pessoa ON pessoa.idpes = school_managers.employee_id
                    WHERE school_managers.chief
                 ) aux
            WHERE escola.cod_escola = aux.cod_escola
              AND escola.ref_idpes_gestor IS NULL
sql;

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
