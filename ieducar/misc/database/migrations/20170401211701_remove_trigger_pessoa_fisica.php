<?php

use Phinx\Migration\AbstractMigration;

class RemoveTriggerPessoaFisica extends AbstractMigration
{
    public function change()
    {
      $this->execute("DROP TRIGGER trg_aft_fisica_historico_campo ON cadastro.fisica;
                      DROP TRIGGER trg_aft_documento ON cadastro.documento;
                      DROP FUNCTION cadastro.fcn_aft_documento();
                      DROP TRIGGER trg_aft_documento_historico_campo ON cadastro.documento;");
    }
}
