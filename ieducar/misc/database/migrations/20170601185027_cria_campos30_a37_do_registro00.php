<?php

use Phinx\Migration\AbstractMigration;

class CriaCampos30A37DoRegistro00 extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN categoria_escola_privada INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN conveniada_com_poder_publico INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN mantenedora_escola_privada INTEGER;
                        ALTER TABLE pmieducar.escola ADD COLUMN cnpj_mantenedora_principal NUMERIC(14,0);");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN categoria_escola_privada;
                        ALTER TABLE pmieducar.escola DROP COLUMN conveniada_com_poder_publico;
                        ALTER TABLE pmieducar.escola DROP COLUMN mantenedora_escola_privada;
                        ALTER TABLE pmieducar.escola DROP COLUMN cnpj_mantenedora_principal;");
    }
}
