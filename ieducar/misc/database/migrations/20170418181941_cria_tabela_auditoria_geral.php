<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaAuditoriaGeral extends AbstractMigration
{
    public function up()
    {
      $this->execute("CREATE TABLE modules.auditoria_geral (
                             usuario_id VARCHAR(300),
                             operacao SMALLINT,
                             rotina VARCHAR(50),
                             valor_antigo JSON,
                             valor_novo JSON,
                             data_hora TIMESTAMP);");
    }

    public function down()
    {
      $this->execute("DROP TABLE modules.auditoria_geral;");
    }
}
