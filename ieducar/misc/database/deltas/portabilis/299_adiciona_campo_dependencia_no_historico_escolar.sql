-- Adiciona campo dependencia na tabela de hostorico escolar para identificar históricos de dependencia
-- @author Paula Bonot <bonot@portabilis.com.br>

ALTER TABLE pmieducar.historico_escolar ADD COLUMN dependencia BOOLEAN;

-- Undo

ALTER TABLE pmieducar.historico_escolar DROP COLUMN dependencia;