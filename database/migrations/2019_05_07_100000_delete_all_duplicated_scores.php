<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DeleteAllDuplicatedScores extends Migration
{
    /**
     * Delete duplicated records from `modules.falta_aluno`.
     *
     * @return void
     */
    private function deleteFromFaltaAlunoWithDiff()
    {
        DB::unprepared(
            '
                delete from modules.falta_aluno 
                where id in (
                    select distinct fa.id
                    from modules.falta_aluno fa
                    inner join pmieducar.matricula m 
                    on m.cod_matricula = fa.matricula_id
                    inner join modules.regra_avaliacao_serie_ano rasa 
                    on rasa.serie_id = m.ref_ref_cod_serie 
                    and m.ano = rasa.ano_letivo
                    inner join modules.regra_avaliacao ra 
                    on ra.id = rasa.regra_avaliacao_id
                    where true
                    and ra.tipo_presenca != fa.tipo_falta
                ) and matricula_id in (
                    select matricula_id
                    from modules.falta_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.falta_aluno`.
     *
     * @return void
     */
    private function deleteFromFaltaAlunoGeneralType()
    {
        DB::unprepared(
            '
                delete from modules.falta_aluno 
                where id in (
                    select distinct fa.id
                    from modules.falta_aluno fa
                    inner join modules.falta_componente_curricular fcc 
                    on fcc.falta_aluno_id = fa.id
                    inner join pmieducar.matricula m 
                    on m.cod_matricula = fa.matricula_id
                    inner join modules.regra_avaliacao_serie_ano rasa 
                    on rasa.serie_id = m.ref_ref_cod_serie 
                    and m.ano = rasa.ano_letivo
                    inner join modules.regra_avaliacao ra 
                    on ra.id = rasa.regra_avaliacao_id
                    where true
                    and fa.tipo_falta = 1
                ) and matricula_id in (
                    select matricula_id
                    from modules.falta_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.falta_aluno`.
     *
     * @return void
     */
    private function deleteFromFaltaAlunoDisciplineType()
    {
        DB::unprepared(
            '
                delete from modules.falta_aluno 
                where id in (
                    select distinct fa.id
                    from modules.falta_aluno fa
                    inner join modules.falta_geral fg
                    on fg.falta_aluno_id = fa.id
                    inner join pmieducar.matricula m 
                    on m.cod_matricula = fa.matricula_id
                    inner join modules.regra_avaliacao_serie_ano rasa 
                    on rasa.serie_id = m.ref_ref_cod_serie 
                    and m.ano = rasa.ano_letivo
                    inner join modules.regra_avaliacao ra 
                    on ra.id = rasa.regra_avaliacao_id
                    where true
                    and fa.tipo_falta = 2
                ) and matricula_id in (
                    select matricula_id
                    from modules.falta_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.falta_aluno`.
     *
     * @return void
     */
    private function deleteMinFaltaAluno()
    {
        DB::unprepared(
            '
                delete from modules.falta_aluno where matricula_id in (
                    select matricula_id
                    from modules.falta_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                )
                and id not in (
                    select max(id) from modules.falta_aluno where matricula_id in (
                        select matricula_id
                        from modules.falta_aluno 
                        group by matricula_id
                        having count(matricula_id) > 1
                    )
                    group by matricula_id
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.nota_aluno`.
     *
     * @return void
     */
    private function deleteMinNotaAluno()
    {
        DB::unprepared(
            '
                delete from modules.nota_aluno where matricula_id in (
                    select matricula_id
                    from modules.nota_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                )
                and id not in (
                    select max(id) from modules.nota_aluno where matricula_id in (
                        select matricula_id
                        from modules.nota_aluno 
                        group by matricula_id
                        having count(matricula_id) > 1
                    )
                    group by matricula_id
                );
            '
        );
    }

    /**
     * Delete duplicated records from `modules.parecer_aluno`.
     *
     * @return void
     */
    private function deleteMinParecerAluno()
    {
        DB::unprepared(
            '
                delete from modules.parecer_aluno where matricula_id in (
                    select matricula_id
                    from modules.parecer_aluno 
                    group by matricula_id
                    having count(matricula_id) > 1
                )
                and id not in (
                    select max(id) from modules.parecer_aluno where matricula_id in (
                        select matricula_id
                        from modules.parecer_aluno 
                        group by matricula_id
                        having count(matricula_id) > 1
                    )
                    group by matricula_id
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
        $this->deleteFromFaltaAlunoWithDiff();
        $this->deleteFromFaltaAlunoGeneralType();
        $this->deleteFromFaltaAlunoDisciplineType();
        $this->deleteMinFaltaAluno();
        $this->deleteMinNotaAluno();
        $this->deleteMinParecerAluno();
    }
}
