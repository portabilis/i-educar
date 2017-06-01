<?php

use Phinx\Migration\AbstractMigration;

class AdicionaSubstituiMenorNotaFormulaMedia extends AbstractMigration
{
  public function up()
  {
    $this->execute("ALTER TABLE modules.formula_media ADD COLUMN substitui_menor_nota_rc SMALLINT NOT NULL DEFAULT 0");
  }
}
