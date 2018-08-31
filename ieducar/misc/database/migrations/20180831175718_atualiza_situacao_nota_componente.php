<?php

use Phinx\Migration\AbstractMigration;

class AtualizaSituacaoNotaComponente extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $query = <<<'SQL'
            update
                modules.nota_componente_curricular_media	
            set
                situacao = (case
                    when matricula_turma.transferido is true then 4
                    when matricula_turma.abandono is true then 6
                    when matricula_turma.falecido is true then 15
                end)
            from
                modules.nota_aluno,
                pmieducar.matricula,
                pmieducar.matricula_turma
            where true
                and nota_componente_curricular_media.nota_aluno_id = nota_aluno.id
                and nota_aluno.matricula_id = matricula.cod_matricula
                and matricula.cod_matricula = matricula_turma.ref_cod_matricula
                and (
                    matricula_turma.transferido is true
                    or matricula_turma.abandono is true
                    or matricula_turma.falecido is true
                )
                and nota_componente_curricular_media.situacao not in (4, 6, 15)
SQL;

        $this->execute($query);

        $query = <<<'SQL'
            update
                nota_componente_curricular_media
            set
                situacao = (case
                    when view_situacao.texto_situacao = 'Aprovado' then 1
                    when view_situacao.texto_situacao = 'Reprovado' then 2
                    when view_situacao.texto_situacao = 'Cursando' then 3
                    when view_situacao.texto_situacao = 'Transferido' then 4
                    when view_situacao.texto_situacao = 'Reclassificado' then 5
                    when view_situacao.texto_situacao = 'Abandono' then 6
                    when view_situacao.texto_situacao = 'Ap. Depen.' then 12
                    when view_situacao.texto_situacao = 'Ap. Cons.' then 13
                    when view_situacao.texto_situacao = 'Rp. Faltas' then 14
                    when view_situacao.texto_situacao = 'Falecido' then 15
                end)
            from
                modules.nota_aluno,
                relatorio.view_situacao
            where true
                and nota_aluno.id = nota_componente_curricular_media.nota_aluno_id
                and nota_aluno.matricula_id = view_situacao.cod_matricula
                and nota_componente_curricular_media.situacao is null;
SQL;

        $this->execute($query);
    }
}
