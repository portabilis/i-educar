<?php

use Phinx\Migration\AbstractMigration;

class AtualizaSequencialRaca extends AbstractMigration
{
    public function change()
    {
        $this->execute("SELECT setval('raca_cod_raca_seq', (SELECT MAX(cod_raca) FROM cadastro.raca)::integer);");
    }
}
