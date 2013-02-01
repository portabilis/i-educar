	-- //

 	--
 	-- Cria indice para tombo na tabela exemplar, otimizando consultas.
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  CREATE INDEX exemplar_tombo_idx
  ON pmieducar.exemplar
  USING btree
  (tombo);

  VACUUM ANALYZE pmieducar.exemplar;
  REINDEX TABLE pmieducar.exemplar;


	-- //@UNDO

  DROP INDEX pmieducar.exemplar_tombo_idx;

	-- //
