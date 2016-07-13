-- Aumenta o tamanho do campo complemento na tabela historico.endereco_pessoa
-- @author Caroline Salib <caroline@portabilis.com.br>


ALTER TABLE historico.endereco_pessoa ALTER COLUMN complemento TYPE CHARACTER VARYING(50);