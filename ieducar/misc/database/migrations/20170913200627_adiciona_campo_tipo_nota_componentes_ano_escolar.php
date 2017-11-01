<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoTipoNotaComponentesAnoEscolar extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.componente_curricular_ano_escolar ADD COLUMN tipo_nota INTEGER;");
    }
}
