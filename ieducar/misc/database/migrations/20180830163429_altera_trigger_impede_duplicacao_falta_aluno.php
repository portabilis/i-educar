<?php

use Phinx\Migration\AbstractMigration;

class AlteraTriggerImpedeDuplicacaoFaltaAluno extends AbstractMigration
{

    public function change()
    {
        $sql =<<<'SQL'
        CREATE OR REPLACE FUNCTION modules.impede_duplicacao_falta_aluno()
  RETURNS trigger AS
$BODY$
                    BEGIN
                        PERFORM * FROM modules.falta_aluno
                        WHERE falta_aluno.matricula_id = NEW.matricula_id
                          AND falta_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela falta_aluno', NEW.matricula_id;
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
