<?php

use Phinx\Migration\AbstractMigration;

class AtualizaCampoDestinacaoLixo extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.escola
                           SET destinacao_lixo = array_append(destinacao_lixo, 1)
                         WHERE lixo_coleta_periodica = 1;
                    
                        UPDATE pmieducar.escola
                           SET destinacao_lixo = array_append(destinacao_lixo, 2)
                         WHERE lixo_queima = 1;
                    
                        UPDATE pmieducar.escola
                           SET destinacao_lixo = array_append(destinacao_lixo, 3)
                         WHERE lixo_joga_outra_area = 1;
                    
                        UPDATE pmieducar.escola
                           SET destinacao_lixo = array_append(destinacao_lixo, 4)
                         WHERE lixo_recicla = 1;
                    
                        UPDATE pmieducar.escola
                           SET destinacao_lixo = array_append(destinacao_lixo, 5)
                         WHERE lixo_enterra = 1;
                         
                         UPDATE pmieducar.escola
                           SET destinacao_lixo = array_append(destinacao_lixo, 6)
                         WHERE lixo_outros = 1;");
    }

    public function down(){
        $this->execute("UPDATE pmieducar.escola
                           SET destinacao_lixo = NULL");
    }
}
