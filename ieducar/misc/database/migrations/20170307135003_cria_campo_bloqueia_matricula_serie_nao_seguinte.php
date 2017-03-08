<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoBloqueiaMatriculaSerieNaoSeguinte extends AbstractMigration
{
  public function up()
  {
    $this->execute("ALTER TABLE pmieducar.instituicao
                      ADD bloqueia_matricula_serie_nao_seguinte BOOLEAN;");
    $this->execute("UPDATE pmieducar.instituicao
                       SET bloqueia_matricula_serie_nao_seguinte = FALSE;");
  }

  public function down()
  {
    $this->execute("ALTER TABLE pmieducar.instituicao
                     DROP COLUMN bloqueia_matricula_serie_nao_seguinte;");
  }
}
