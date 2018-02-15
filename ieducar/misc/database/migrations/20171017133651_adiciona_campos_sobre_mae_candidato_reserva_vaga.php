<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCamposSobreMaeCandidatoReservaVaga extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.candidato_reserva_vaga ADD COLUMN mae_trabalha BOOLEAN DEFAULT FALSE;");
        $this->execute("ALTER TABLE pmieducar.candidato_reserva_vaga ADD COLUMN mae_fez_pre_natal BOOLEAN DEFAULT FALSE;");
    }
}
