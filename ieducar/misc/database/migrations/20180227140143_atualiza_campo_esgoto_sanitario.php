<?php

use Phinx\Migration\AbstractMigration;

class AtualizaCampoEsgotoSanitario extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.escola
                           SET esgoto_sanitario = array_append(esgoto_sanitario, 1)
                         WHERE esgoto_rede_publica = 1;
                    
                        UPDATE pmieducar.escola
                           SET esgoto_sanitario = array_append(esgoto_sanitario, 2)
                         WHERE esgoto_fossa = 1;
                    
                        UPDATE pmieducar.escola
                           SET esgoto_sanitario = array_append(esgoto_sanitario, 3)
                         WHERE esgoto_inexistente = 1;");
    }

    public function down(){
        $this->execute("UPDATE pmieducar.escola
                           SET esgoto_sanitario = NULL");
    }
}
