<?php

use Phinx\Migration\AbstractMigration;

class AdicionaAtividadeComplementarEmTipoEnsino extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.tipo_ensino ADD COLUMN atividade_complementar BOOLEAN DEFAULT FALSE;");
    }
}
