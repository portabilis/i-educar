<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerParaImpedirDuplicacaoDeNotas extends AbstractMigration
{
    public function up()
    {
        $sql = <<<'SQL'
                    CREATE OR REPLACE FUNCTION modules.impede_duplicacao_nota_aluno()
                        RETURNS TRIGGER AS $$
                    BEGIN
                        PERFORM * FROM modules.nota_aluno
                                WHERE nota_aluno.matricula_id = NEW.matricula_id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela nota_aluno', NEW.matricula_id;
                        END IF;
                    
                        RETURN NEW;
                    END;
                    $$ language 'plpgsql';
                    
                    CREATE TRIGGER impede_duplicacao_nota_aluno 
                        BEFORE INSERT OR UPDATE ON modules.nota_aluno FOR EACH ROW EXECUTE PROCEDURE modules.impede_duplicacao_nota_aluno();
SQL;
        $this->execute($sql);
    }
}
