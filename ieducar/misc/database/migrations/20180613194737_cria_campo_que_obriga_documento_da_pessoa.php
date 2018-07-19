<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoQueObrigaDocumentoDaPessoa extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN obrigar_documento_pessoa BOOLEAN DEFAULT FALSE;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN obrigar_documento_pessoa;");
    }
}
