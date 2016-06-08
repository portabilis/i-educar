-- Cria tabela de relacao entre categoria e obra
-- @author Maurício Citadini Biléssimo <mauricio@portabilis.com.br>

ALTER TABLE modules.ficha_medica_aluno ALTER COLUMN altura TYPE varchar(4);
ALTER TABLE modules.ficha_medica_aluno ALTER COLUMN peso TYPE varchar(7);
ALTER TABLE modules.ficha_medica_aluno ALTER COLUMN grupo_sanguineo TYPE varchar(2);
ALTER TABLE modules.ficha_medica_aluno ALTER COLUMN fator_rh TYPE varchar(1);