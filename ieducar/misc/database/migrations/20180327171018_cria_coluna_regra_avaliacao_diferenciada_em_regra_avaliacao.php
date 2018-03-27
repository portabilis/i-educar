]<?php

use Phinx\Migration\AbstractMigration;

class CriaColunaRegraAvaliacaoDiferenciadaEmRegraAvaliacao extends AbstractMigration
{
    public function change()
    {
        $this->execute('
            ALTER TABLE modules.regra_avaliacao ADD regra_diferenciada_id INTEGER;
            ALTER TABLE modules.regra_avaliacao
            ADD CONSTRAINT regra_diferenciada_fk
            FOREIGN KEY (regra_diferenciada_id)
            REFERENCES modules.regra_avaliacao (id);
        ');
    }
}
