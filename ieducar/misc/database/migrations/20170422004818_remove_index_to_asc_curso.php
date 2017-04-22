<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexToAscCurso extends AbstractMigration
{
    public function change()
    {
      $this->execute("DROP INDEX pmieducar.i_curso_sgl_curso_asc;");
      $this->execute("DROP INDEX pmieducar.i_curso_nm_curso_asc;");
      $this->execute("DROP INDEX pmieducar.i_curso_objetivo_curso_asc;");
    }
}
