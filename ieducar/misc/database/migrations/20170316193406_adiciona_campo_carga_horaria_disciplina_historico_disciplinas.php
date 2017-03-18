<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoCargaHorariaDisciplinaHistoricoDisciplinas extends AbstractMigration
{
   public function up()
     {
         $this->execute("ALTER TABLE pmieducar.historico_disciplinas ADD COLUMN carga_horaria_disciplina integer;");
     }
}
