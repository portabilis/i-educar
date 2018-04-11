<?php

use Phinx\Migration\AbstractMigration;

class CriaCamposMateriaisDidaticosEspecificos extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN materiais_didaticos_especificos INTEGER;");
        $this->execute("UPDATE pmieducar.escola
                           SET materiais_didaticos_especificos = CASE WHEN didatico_nao_utiliza = 1 THEN 1
                                                                      WHEN didatico_quilombola = 1 THEN 2
                                                                      WHEN didatico_indigena = 1 THEN 3
                                                                  END;");
    }
    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN materiais_didaticos_especificos;");
    }
}