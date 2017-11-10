<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoMoradiaReservaVaga extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE pmieducar.candidato_reserva_vaga ADD COLUMN moradia SMALLINT;');
    }
}
