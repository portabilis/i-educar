<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuConfigMovimentoGeral extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998867, 25, 2,'Configuração movimento geral', 'module/Configuracao/ConfiguracaoMovimentoGeral', null, 3);");

        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998867, 9998867, 999909, 'Configuração movimento geral', 3, 'module/Configuracao/ConfiguracaoMovimentoGeral', '_self', 1, 18);");

        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998867,1,1,1);");
    }
}
