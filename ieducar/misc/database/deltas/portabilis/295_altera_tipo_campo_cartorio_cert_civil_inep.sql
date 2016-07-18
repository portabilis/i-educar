-- Aumenta tipo do campo cartorio_cert_civil_inep para aceitar zero a esquerda
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE cadastro.documento ALTER COLUMN cartorio_cert_civil_inep TYPE character varying(20);