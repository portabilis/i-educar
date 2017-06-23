<?php

use Phinx\Migration\AbstractMigration;

class AlteraCampoMantenedoraEscolaParaArray extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN mantenedora_escola_privada;");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN mantenedora_escola_privada INTEGER[];");
    }
}
