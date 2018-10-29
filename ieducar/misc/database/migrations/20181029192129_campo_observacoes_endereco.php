<?php

use Phinx\Migration\AbstractMigration;

class CampoObservacoesEndereco extends AbstractMigration
{
    public function change()
    {
        $this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'cadastro']));

        $this->table('endereco_pessoa')
            ->addColumn('observacoes', 'text', ['null' => true])
            ->update();
    }
}
