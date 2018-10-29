<?php

use Phinx\Migration\AbstractMigration;

class AtualizaSubstituiMenorNota extends AbstractMigration
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
                modules.regra_avaliacao_recuperacao
            set
                substitui_menor_nota = true
            from
                 modules.regra_avaliacao
            where true
                and regra_avaliacao.id = regra_avaliacao_recuperacao.regra_avaliacao_id
                and regra_avaliacao.tipo_recuperacao_paralela = 2;
SQL;

        $this->execute($query);
    }
}
