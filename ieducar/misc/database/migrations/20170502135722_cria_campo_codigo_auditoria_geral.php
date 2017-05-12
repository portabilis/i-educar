<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoCodigoAuditoriaGeral extends AbstractMigration
{
    public function change()
    {
      $this->execute('ALTER TABLE modules.auditoria_geral ADD codigo INTEGER;');
    }
}
