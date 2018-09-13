<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoParaDesconsiderarDeficienciaNaRegraDiferenciada extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE cadastro.deficiencia ADD COLUMN desconsidera_regra_diferenciada BOOLEAN DEFAULT false;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE cadastro.deficiencia DROP COLUMN desconsidera_regra_diferenciada;');
    }
}
