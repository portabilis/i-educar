<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigraTurnoDeMatriculaParaMatriculaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<'SQL'
            UPDATE
              pmieducar.matricula_turma
            SET
              turno_id = tmp.turno_id
            FROM
                 (
                   SELECT
                      turno_id,
                      cod_matricula
                   FROM
                      pmieducar.matricula
                   WHERE TRUE
                      AND turno_id IS NOT NULL
                 ) AS tmp
            WHERE TRUE
              AND pmieducar.matricula_turma.ref_cod_matricula = tmp.cod_matricula
              AND pmieducar.matricula_turma.ativo = 1
SQL;
        DB::statement($sql);
    }
}
