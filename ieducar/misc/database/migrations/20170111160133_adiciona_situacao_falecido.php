<?php

use Phinx\Migration\AbstractMigration;

class AdicionaSituacaoFalecido extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO relatorio.situacao_matricula VALUES (15, 'Falecido');");
    }
}
