<?php

use Phinx\Migration\AbstractMigration;

class AdicionaAssentamentoTipoLogradouro extends AbstractMigration
{

    public function change()
    {
        $this->execute('INSERT INTO urbano.tipo_logradouro (idtlog, descricao) VALUES (\'ASSEN\', \'Assentamento\');');
    }
}
