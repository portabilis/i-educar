<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCamposArrayNaEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN abastecimento_agua INTEGER[];");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN abastecimento_energia INTEGER[];");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN esgoto_sanitario INTEGER[];");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN destinacao_lixo INTEGER[];");
    }
    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN abastecimento_agua;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN abastecimento_energia;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN esgoto_sanitario;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN destinacao_lixo;");
    }
}
