<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoNumericoDeEtapasNaTabelaModulo extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.modulo ADD COLUMN num_etapas numeric(2,0) NOT NULL DEFAULT 0;');
    }
}
