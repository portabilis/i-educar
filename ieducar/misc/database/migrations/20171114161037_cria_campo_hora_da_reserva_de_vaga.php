<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoHoraDaReservaDeVaga extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.candidato_reserva_vaga ADD COLUMN hora_solicitacao TIME");
    }
}
