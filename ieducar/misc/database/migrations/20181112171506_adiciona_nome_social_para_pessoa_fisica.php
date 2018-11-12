<?php

use Phinx\Migration\AbstractMigration;

class AdicionaNomeSocialParaPessoaFisica extends AbstractMigration
{
    public function change()
    {
        $this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'cadastro']));
        $table = $this->table('fisica');
        $table->addColumn('nome_social', 'string', ['limit' => 150, 'null' => true])
            ->update();
    }
}
