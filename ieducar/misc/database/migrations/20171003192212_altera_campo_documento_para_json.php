<?php

use Phinx\Migration\AbstractMigration;

class AlteraCampoDocumentoParaJson extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.aluno
                        ALTER COLUMN url_documento
                         TYPE json
                        USING CASE WHEN url_documento != ''
                                        THEN replace('[{\"' || replace(url_documento, ',', '\"},{\"') || '\"}]', '\"http', '\"url\" : \"http')::json
                                   ELSE NULL
                              END;");
    }
}
