<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoTipoMediacaoDidaticoPedagogico extends AbstractMigration
{
  public function up()
  {
    $this->execute("ALTER TABLE pmieducar.turma
                      ADD COLUMN tipo_mediacao_didatico_pedagogico INTEGER;");

    $this->execute("UPDATE pmieducar.turma
                       SET tipo_mediacao_didatico_pedagogico = 1
                     WHERE ano > 2015;");
  }
}