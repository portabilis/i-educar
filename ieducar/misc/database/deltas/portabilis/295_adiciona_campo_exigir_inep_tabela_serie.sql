-- Adiciona campo exigir_serie na tabela serie
-- @author Paula Bonot <bonot@portabilis.com.br>

ALTER TABLE pmieducar.serie ADD COLUMN exigir_inep boolean;

-- Undo

ALTER TABLE pmieducar.serie DROP COLUMN exigir_inep;