<?php

use Phinx\Migration\AbstractMigration;

class AtualizaCampoAbastecimentoAgua extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.escola
                           SET abastecimento_agua = array_append(abastecimento_agua, 1)
                         WHERE agua_rede_publica = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_agua = array_append(abastecimento_agua, 2)
                         WHERE agua_poco_artesiano = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_agua = array_append(abastecimento_agua, 3)
                         WHERE agua_cacimba_cisterna_poco = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_agua = array_append(abastecimento_agua, 4)
                         WHERE agua_fonte_rio = 1;
                    
                        UPDATE pmieducar.escola
                           SET abastecimento_agua = array_append(abastecimento_agua, 5)
                         WHERE agua_inexistente = 1;");
    }

    public function down(){
        $this->execute("UPDATE pmieducar.escola
                           SET abastecimento_agua = NULL");
    }
}
