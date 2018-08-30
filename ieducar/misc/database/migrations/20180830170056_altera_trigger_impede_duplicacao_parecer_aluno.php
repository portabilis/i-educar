<?php

use Phinx\Migration\AbstractMigration;

class AlteraTriggerImpedeDuplicacaoParecerAluno extends AbstractMigration
{
    public function change()
    {
        $sql =<<<'SQL'
        CREATE OR REPLACE FUNCTION modules.impede_duplicacao_parecer_aluno()
  RETURNS trigger AS
$BODY$
                    BEGIN
                        PERFORM * FROM modules.parecer_aluno
                                 WHERE parecer_aluno.matricula_id = NEW.matricula_id
                                   AND parecer_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela parecer_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;

SQL;

        $this->execute($sql);
    }
}
