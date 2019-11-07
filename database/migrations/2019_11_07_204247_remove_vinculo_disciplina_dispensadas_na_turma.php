<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveVinculoDisciplinaDispensadasNaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE modules.professor_turma
            SET updated_at = NOW()
            FROM modules.professor_turma_disciplina, pmieducar.turma
            WHERE professor_turma_disciplina.professor_turma_id = professor_turma.id
            AND turma.ref_cod_disciplina_dispensada = professor_turma_disciplina.componente_curricular_id
            AND turma.cod_turma = professor_turma.turma_id;
        ");

        DB::statement("
            DELETE FROM modules.professor_turma_disciplina
            USING modules.professor_turma
            JOIN pmieducar.turma ON turma.cod_turma = professor_turma.turma_id
            WHERE professor_turma_disciplina.professor_turma_id = professor_turma.id
            AND turma.ref_cod_disciplina_dispensada = professor_turma_disciplina.componente_curricular_id;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
