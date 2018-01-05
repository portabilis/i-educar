<?php

use Phinx\Migration\AbstractMigration;

class AlteraCampoLaudoParaJson extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.aluno
                        ALTER COLUMN url_laudo_medico
                         TYPE json
                        USING CASE WHEN url_laudo_medico != ''
                                        THEN replace('[{\"' || replace(url_laudo_medico, ',', '\"},{\"') || '\"}]', '\"http', '\"url\" : \"http')::json
                                   ELSE NULL
                              END;");
    }
}
