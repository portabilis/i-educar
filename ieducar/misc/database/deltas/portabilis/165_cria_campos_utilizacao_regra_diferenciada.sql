--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.escola ADD COLUMN utiliza_regra_diferenciada BOOLEAN;

ALTER TABLE pmieducar.serie ADD COLUMN regra_avaliacao_diferenciada_id integer;
ALTER TABLE pmieducar.serie ADD CONSTRAINT serie_regra_avaliacao_diferenciada_fk
  FOREIGN KEY(regra_avaliacao_diferenciada_id)
  REFERENCES modules.regra_avaliacao(id)
  ON DELETE RESTRICT;