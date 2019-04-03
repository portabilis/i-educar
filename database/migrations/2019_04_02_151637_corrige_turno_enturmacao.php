<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorrigeTurnoEnturmacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $sql = <<<'SQL'
            UPDATE pmieducar.matricula_turma
               SET turno_id = NULL
              FROM pmieducar.matricula_turma mt
             INNER JOIN pmieducar.turma t ON t.cod_turma = mt.ref_cod_turma
             WHERE t.turma_turno_id <> 4
               AND matricula_turma.ref_cod_matricula = mt.ref_cod_matricula
               AND matricula_turma.sequencial = mt.sequencial
               AND mt.turno_id IS NOT NULL
SQL;
        DB::statement($sql);
    }
}
