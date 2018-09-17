<?php

use Phinx\Migration\AbstractMigration;

class AlteraTriggerImpedeDuplicacaoNotaAluno extends AbstractMigration
{
    public function change()
    {
        $sql =<<<'SQL'
        CREATE OR REPLACE FUNCTION modules.impede_duplicacao_nota_aluno()
  RETURNS trigger AS
$BODY$
                    BEGIN
                        PERFORM * FROM modules.nota_aluno
                                WHERE nota_aluno.matricula_id = NEW.matricula_id
                                  AND nota_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela nota_aluno', NEW.matricula_id;
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
