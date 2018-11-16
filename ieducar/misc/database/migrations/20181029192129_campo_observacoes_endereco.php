<?php

use Phinx\Migration\AbstractMigration;

class CampoObservacoesEndereco extends AbstractMigration
{
    public function change()
    {
        $this->table('cadastro.endereco_pessoa')
            ->addColumn('observacoes', 'text', ['null' => true])
            ->update();
    }
}
