<?php

use Phinx\Migration\AbstractMigration;

class CriaParametroUsarCargaHoraria extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE pmieducar.instituicao ADD column permitir_carga_horaria BOOLEAN DEFAULT false;');
    }
}
