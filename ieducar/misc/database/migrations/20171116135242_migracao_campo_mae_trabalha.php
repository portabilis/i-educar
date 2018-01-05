<?php

use Phinx\Migration\AbstractMigration;

class MigracaoCampoMaeTrabalha extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE pmieducar.candidato_reserva_vaga
                               SET membros_trabalham = 1
                             WHERE mae_trabalha = TRUE;");
        $this->execute("ALTER TABLE pmieducar.candidato_reserva_vaga DROP COLUMN mae_trabalha;");
    }
}
