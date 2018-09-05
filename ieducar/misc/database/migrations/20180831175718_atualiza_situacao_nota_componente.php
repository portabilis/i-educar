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
                situacao = matricula.aprovado
            from
                modules.nota_aluno,
                pmieducar.matricula
            where true
                and nota_componente_curricular_media.nota_aluno_id = nota_aluno.id
                and nota_aluno.matricula_id = matricula.cod_matricula
                and matricula.aprovado in (4, 5, 6, 15)
                and nota_componente_curricular_media.situacao not in (4, 5, 6, 15)
SQL;

        $this->execute($query);

        $query = <<<'SQL'
            update
                nota_componente_curricular_media
            set
                situacao = matricula.aprovado
            from
                modules.nota_aluno,
                pmieducar.matricula
            where true
                and nota_aluno.id = nota_componente_curricular_media.nota_aluno_id
                and nota_aluno.matricula_id = matricula.cod_matricula
                and nota_componente_curricular_media.situacao is null;
SQL;

        $this->execute($query);
    }
}
