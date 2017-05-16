<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoBloqueiaEmprestimoEmAtraso extends AbstractMigration{
  public function up(){
    $this->execute("ALTER TABLE pmieducar.biblioteca
                      ADD bloqueia_emprestimo_em_atraso BOOLEAN;");
    $this->execute("UPDATE pmieducar.biblioteca
                       SET bloqueia_emprestimo_em_atraso = FALSE;");
  }

  public function down(){
    $this->execute("ALTER TABLE pmieducar.biblioteca
                     DROP COLUMN bloqueia_emprestimo_em_atraso;");
  }
}
