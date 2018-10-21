<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaParaExibirProfessoresAlocados extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN exibir_apenas_professores_alocados BOOLEAN DEFAULT false;");
        $this->execute("COMMENT ON COLUMN pmieducar.instituicao.exibir_apenas_professores_alocados IS 'Para filtros de emissão de relatórios'");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN exibir_apenas_professores_alocados;");
    }
}
