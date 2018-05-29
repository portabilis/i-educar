<?php

use Phinx\Migration\AbstractMigration;

class RemoveCampoTipoInstituicaoCursoSuperior extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN tipo_instituicao_curso_superior_1;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN tipo_instituicao_curso_superior_2;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN tipo_instituicao_curso_superior_3;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN tipo_instituicao_curso_superior_1 SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN tipo_instituicao_curso_superior_2 SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN tipo_instituicao_curso_superior_3 SMALLINT;');
    }
}