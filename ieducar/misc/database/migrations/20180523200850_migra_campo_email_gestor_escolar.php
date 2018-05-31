<?php

use Phinx\Migration\AbstractMigration;

class MigraCampoEmailGestorEscolar extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE pmieducar.escola
                        set email_gestor = (select email from cadastro.pessoa where idpes = ref_idpes_gestor)
                        where ref_idpes_gestor is not null
        ");
    }
}
