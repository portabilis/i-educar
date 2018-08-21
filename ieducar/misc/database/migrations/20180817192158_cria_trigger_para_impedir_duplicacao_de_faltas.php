<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerParaImpedirDuplicacaoDeFaltas extends AbstractMigration
{
    public function up()
    {
        $sql = <<<'SQL'
                    CREATE OR REPLACE FUNCTION modules.impede_duplicacao_falta_aluno()
                        RETURNS TRIGGER AS $$
                    BEGIN
                        PERFORM * FROM modules.falta_aluno
                        WHERE falta_aluno.matricula_id = NEW.matricula_id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela falta_aluno', NEW.matricula_id;
                        END IF;
                    
                        RETURN NEW;
                    END;
                    $$ language 'plpgsql';
                    
                    CREATE TRIGGER impede_duplicacao_falta_aluno BEFORE INSERT OR UPDATE ON modules.falta_aluno FOR EACH ROW EXECUTE                            PROCEDURE modules.impede_duplicacao_falta_aluno();
SQL;
        $this->execute($sql);
    }
}
