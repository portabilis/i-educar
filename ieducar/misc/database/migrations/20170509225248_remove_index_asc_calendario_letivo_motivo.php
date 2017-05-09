<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexAscCalendarioLetivoMotivo extends AbstractMigration
{
    public function change()
    {
        $this->execute("DROP INDEX pmieducar.i_calendario_dia_motivo_sigla_asc");
    }
}
