<?php

use Phinx\Migration\AbstractMigration;

class AdicionaNomeSocialParaPessoaFisica extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('cadastro.fisica');
        $table->addColumn('nome_social', 'string', ['limit' => 150, 'null' => true])
            ->update();
    }
}
