-- Aumenta tipo do campo cartorio_cert_civil_inep para aceitar zero a esquerda
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE pmieducar.aluno ADD COLUMN recebe_escolarizacao_em_outro_espaco SMALLINT;