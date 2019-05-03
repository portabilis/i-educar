<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DeleteDuplicatedScores extends Migration
{
    /**
     * Delete duplicated records from `modules.falta_aluno`.
     *
     * @return void
     */
    private function deleteFromFaltaAluno()
    {
        DB::unprepared(
            '
                delete from modules.falta_aluno fa
                where matricula_id in (
                    select matricula_id
                    from modules.falta_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                )
                and not exists (
                    select 1 from modules.falta_componente_curricular where falta_aluno_id = fa.id
                )
                and not exists (
                    select 1 from modules.falta_geral where falta_aluno_id = fa.id
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.nota_aluno`.
     *
     * @return void
     */
    private function deleteFromNotaAluno()
    {
        DB::unprepared(
            '
                delete from modules.nota_aluno na
                where matricula_id in (
                    select matricula_id
                    from modules.nota_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                )
                and not exists (
                    select 1 from modules.nota_componente_curricular where nota_aluno_id = na.id
                )
                and not exists (
                    select 1 from modules.nota_componente_curricular_media where nota_aluno_id = na.id
                )
                and not exists (
                    select 1 from modules.nota_geral where nota_aluno_id = na.id
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.parecer_aluno`.
     *
     * @return void
     */
    private function deleteFromParecerAluno()
    {
        DB::unprepared(
            '
                delete from modules.parecer_aluno pa
                where matricula_id in (
                    select matricula_id
                    from modules.parecer_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                )
                and not exists (
                    select 1 from modules.parecer_componente_curricular where parecer_aluno_id = pa.id
                )
                and not exists (
                    select 1 from modules.parecer_geral where parecer_aluno_id = pa.id
                );
            '
        );
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->deleteFromFaltaAluno();
        $this->deleteFromNotaAluno();
        $this->deleteFromParecerAluno();
    }
}
