<?php

use Phinx\Migration\AbstractMigration;

class AdicionaNomeSocialParaPessoaFisica extends AbstractMigration
{
    public function change()
    {
        
        $this->execute(
            "
                ALTER TABLE cadastro.fisica 
                ADD COLUMN nome_social varchar(150) NULL;
            "
        );
    }
}
