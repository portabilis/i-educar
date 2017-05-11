<?php

use Phinx\Migration\AbstractMigration;

class DropNotNullDaColunaHistoricoLogradouro extends AbstractMigration
{
    public function change(){
        $this->execute("ALTER table historico.logradouro alter column ident_oficial DROP NOT NULL;");
    }
}
