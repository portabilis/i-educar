<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerParaImpedirDuplicacaoDePareceres extends AbstractMigration
{
    public function up()
    {
        $sql = <<<'SQL'
                    CREATE OR REPLACE FUNCTION modules.impede_duplicacao_parecer_aluno()
                        RETURNS TRIGGER AS $$
                    BEGIN
                        PERFORM * FROM modules.parecer_aluno
                                 WHERE parecer_aluno.matricula_id = NEW.matricula_id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela parecer_aluno', NEW.matricula_id;
                        END IF;
                
                        RETURN NEW;
                    END;
                $$ language 'plpgsql';
                
                    CREATE TRIGGER impede_duplicacao_parecer_aluno BEFORE INSERT OR UPDATE ON modules.parecer_aluno FOR EACH ROW EXECUTE                        PROCEDURE modules.impede_duplicacao_parecer_aluno();
SQL;
        $this->execute($sql);
    }
}
