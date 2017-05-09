<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexAscEscolaComplemento extends AbstractMigration
{
    public function change()
    {
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_bairro_asc");
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_cep_asc");
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_complemento_asc");
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_email_asc");
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_logradouro_asc");
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_municipio_asc");
        $this->execute("DROP INDEX pmieducar.i_escola_complemento_nm_escola_asc");
    }
}