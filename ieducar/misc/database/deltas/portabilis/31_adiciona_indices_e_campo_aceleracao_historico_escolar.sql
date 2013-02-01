 	-- //

 	--
 	-- Na tabela historico escolar, cria indices para , otimizando consultas, como relatórios. Alem de adicionar o campo aceleracao
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  CREATE INDEX historico_escolar_ano_idx
  ON pmieducar.historico_escolar
  USING btree
  (ano);

  CREATE INDEX historico_escolar_ativo_idx
  ON pmieducar.historico_escolar
  USING btree
  (ativo);

  CREATE INDEX historico_escolar_nm_serie_idx
  ON pmieducar.historico_escolar
  USING btree
  (nm_serie);

  ALTER TABLE pmieducar.historico_escolar ADD COLUMN aceleracao integer;

  VACUUM ANALYZE pmieducar.historico_escolar;
  REINDEX TABLE pmieducar.historico_escolar;


	-- //@UNDO

  DROP INDEX pmieducar.historico_escolar_ano_idx;
  DROP INDEX pmieducar.historico_escolar_ativo_idx;
  DROP INDEX pmieducar.historico_escolar_nm_serie_idx;
  ALTER TABLE pmieducar.historico_escolar DROP COLUMN aceleracao;

	-- //
