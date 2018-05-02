<?php

use Phinx\Migration\AbstractMigration;

class AjustaOnDeleteConstraintCursoSuperior extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE pmieducar.servidor DROP CONSTRAINT codigo_curso_superior_1_fk;
             ALTER TABLE pmieducar.servidor DROP CONSTRAINT codigo_curso_superior_2_fk;
             ALTER TABLE pmieducar.servidor DROP CONSTRAINT codigo_curso_superior_3_fk;
             
             ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_1_fk FOREIGN KEY (codigo_curso_superior_1)
                                         REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE
                                         ON UPDATE NO ACTION ON DELETE SET NULL;
             
             ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_2_fk FOREIGN KEY (codigo_curso_superior_2)
                                         REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE
                                         ON UPDATE NO ACTION ON DELETE SET NULL;
             
             ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_3_fk FOREIGN KEY (codigo_curso_superior_3)
                                         REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE
                                         ON UPDATE NO ACTION ON DELETE SET NULL;"
        );
    }

    public function down()
    {
        $this->execute(
            "ALTER TABLE pmieducar.servidor DROP CONSTRAINT codigo_curso_superior_1_fk;
             ALTER TABLE pmieducar.servidor DROP CONSTRAINT codigo_curso_superior_2_fk;
             ALTER TABLE pmieducar.servidor DROP CONSTRAINT codigo_curso_superior_3_fk;
             
             ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_1_fk FOREIGN KEY (codigo_curso_superior_1)
                                         REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE
                                         ON UPDATE NO ACTION ON DELETE NO ACTION;
             
             ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_2_fk FOREIGN KEY (codigo_curso_superior_2)
                                         REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE
                                         ON UPDATE NO ACTION ON DELETE NO ACTION;
             
             ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_3_fk FOREIGN KEY (codigo_curso_superior_3)
                                         REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE
                                         ON UPDATE NO ACTION ON DELETE NO ACTION;"
        );
    }
}
