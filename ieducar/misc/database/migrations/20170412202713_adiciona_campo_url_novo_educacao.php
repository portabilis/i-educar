<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoUrlNovoEducacao extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao
                          ADD COLUMN url_novo_educacao VARCHAR(100);");
    }
}
