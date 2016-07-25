-- Cria campos data_exclusao e ref_usuario_exc na tabela de pessao físca
-- @author Maurício Citadini Biléssimo <mauricio@portabilis.com.br>

ALTER TABLE cadastro.fisica ADD COLUMN ref_usuario_exc integer;
ALTER TABLE cadastro.fisica ADD COLUMN data_exclusao timestamp;