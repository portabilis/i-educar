-- //

--
-- Adiciona uma foreign key entre as tabelas pmieducar.serie e 
-- modules.regra_avaliacao
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

-- Cria um índice único, pois a chave primária é composta
CREATE UNIQUE INDEX regra_avaliacao_id_key
  ON modules.regra_avaliacao USING btree (id);

-- Adiciona campo para foreign key
ALTER TABLE pmieducar.serie ADD COLUMN regra_avaliacao_id integer;

ALTER TABLE pmieducar.serie
  ADD CONSTRAINT serie_regra_avaliacao_fk
  FOREIGN KEY(regra_avaliacao_id)
  REFERENCES modules.regra_avaliacao(id)
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT;
  
-- //@UNDO

ALTER TABLE pmieducar.serie
  DROP CONSTRAINT serie_regra_avaliacao_fk;

ALTER TABLE pmieducar.serie DROP COLUMN regra_avaliacao_id;

DROP INDEX modules.regra_avaliacao_id_key;

-- //