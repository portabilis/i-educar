<?php

use Phinx\Migration\AbstractMigration;

class AtualizaCampoAbastecimentoEnergia extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.escola
                           SET abastecimento_energia = array_append(abastecimento_energia, 1)
                         WHERE energia_rede_publica = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_energia = array_append(abastecimento_energia, 2)
                         WHERE energia_gerador = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_energia = array_append(abastecimento_energia, 3)
                         WHERE energia_outros = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_energia = array_append(abastecimento_energia, 4)
                         WHERE energia_inexistente = 1;");
    }

    public function down(){
        $this->execute("UPDATE pmieducar.escola
                           SET abastecimento_energia = NULL");
    }
}
