<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoZonaLozalizacaoCenso extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE cadastro.fisica ADD COLUMN zona_localizacao_censo INTEGER;");
    }
}
