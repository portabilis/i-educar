-- Cria campos usuario e escola na tabela instituicao documentacao
-- @author Paula Bonot <bonot@portabilis.com.br>

ALTER TABLE pmieducar.instituicao_documentacao ADD COLUMN ref_usuario_cad INTEGER DEFAULT 0 NOT NULL;
ALTER TABLE pmieducar.instituicao_documentacao ADD COLUMN ref_cod_escola INTEGER;