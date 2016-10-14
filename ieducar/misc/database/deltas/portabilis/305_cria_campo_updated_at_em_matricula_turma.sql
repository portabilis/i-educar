-- Cria campo updated_at em matricula_turma
-- @author Caroline Salib <carolne@portabilis.com.br>

-- adiciona coluna na tabela matricula_turma
ALTER TABLE pmieducar.matricula_turma ADD COLUMN updated_at TIMESTAMP;

-- adiciona trigger para atualizar a coluna updated_at
CREATE OR REPLACE FUNCTION pmieducar.updated_at_matricula_turma() RETURNS TRIGGER AS $$ BEGIN NEW.updated_at = now(); RETURN NEW; END; $$ LANGUAGE 'plpgsql';


CREATE TRIGGER trigger_updated_at_matricula_turma
BEFORE
UPDATE ON pmieducar.matricula_turma
FOR EACH ROW EXECUTE PROCEDURE pmieducar.updated_at_matricula_turma();


-- adiciona coluna na tabela matricula
ALTER TABLE pmieducar.matricula ADD COLUMN updated_at TIMESTAMP;

-- adiciona trigger para atualizar a coluna updated_at
CREATE OR REPLACE FUNCTION pmieducar.updated_at_matricula() RETURNS TRIGGER AS $$ BEGIN NEW.updated_at = now(); RETURN NEW; END; $$ LANGUAGE 'plpgsql';


CREATE TRIGGER trigger_updated_at_matricula
BEFORE
UPDATE ON pmieducar.matricula
FOR EACH ROW EXECUTE PROCEDURE pmieducar.updated_at_matricula();