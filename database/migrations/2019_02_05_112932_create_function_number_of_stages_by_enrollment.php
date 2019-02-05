<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionNumberOfStagesByEnrollment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE FUNCTION number_of_stages_by_enrollment(enrollmentId integer) RETURNS bigint
            LANGUAGE sql
            AS $_$

            SELECT COALESCE((
                    SELECT count(1)
                    FROM pmieducar.curso as c
                    INNER JOIN pmieducar.ano_letivo_modulo as anm
                    ON anm.ref_ref_cod_escola = matricula.ref_ref_cod_escola
                    AND anm.ref_ano = matricula.ano
                    WHERE c.padrao_ano_escolar = 1
                    AND matricula.ref_cod_curso = c.cod_curso
                ),0) +
                COALESCE((
                    SELECT COUNT(1)
                    FROM pmieducar.turma as t
                    INNER JOIN pmieducar.curso as c
                    ON t.ref_cod_curso = c.cod_curso
                    INNER JOIN pmieducar.turma_modulo as tm
                    ON tm.ref_cod_turma = t.cod_turma
                    WHERE c.padrao_ano_escolar = 0
                    AND t.cod_turma = (
                        SELECT matricula_turma.ref_cod_turma FROM pmieducar.matricula_turma
                        WHERE matricula_turma.ref_cod_matricula = matricula.cod_matricula
                        ORDER BY ativo DESC, data_enturmacao DESC
                        LIMIT 1
                    )
                ),0) AS etapas
            FROM pmieducar.matricula
            WHERE cod_matricula = enrollmentId;
            $_$;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            DROP FUNCTION number_of_stages_by_enrollment(int);
        ');
    }
}
