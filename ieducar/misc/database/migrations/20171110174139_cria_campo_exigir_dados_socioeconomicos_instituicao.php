<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoExigirDadosSocioeconomicosInstituicao extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE pmieducar.instituicao ADD COLUMN exigir_dados_socioeconomicos BOOLEAN DEFAULT false;');
    }
}
