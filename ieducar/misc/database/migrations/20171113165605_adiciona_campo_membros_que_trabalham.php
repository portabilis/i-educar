<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoMembrosQueTrabalham extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.candidato_reserva_vaga ADD COLUMN membros_trabalham SMALLINT;");
    }
}
