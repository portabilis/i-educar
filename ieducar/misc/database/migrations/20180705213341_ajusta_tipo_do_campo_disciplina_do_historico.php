<?php

use Phinx\Migration\AbstractMigration;

class AjustaTipoDoCampoDisciplinaDoHistorico extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE pmieducar.historico_disciplinas ALTER COLUMN nm_disciplina TYPE TEXT;');
    }
}
