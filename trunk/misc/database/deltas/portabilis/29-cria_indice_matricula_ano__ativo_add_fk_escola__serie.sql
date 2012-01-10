 	-- //
  
 	--
 	-- Na tabela matricula, cria indices para ano, ativo e adiciona FK's para serie, escola, otimizando consultas, como relatórios. 
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  

  ALTER TABLE pmieducar.matricula ADD CONSTRAINT matricula_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola)
  REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
  ON UPDATE RESTRICT ON DELETE RESTRICT;

  ALTER TABLE pmieducar.matricula ADD CONSTRAINT matricula_ref_ref_cod_serie_fkey FOREIGN KEY (ref_ref_cod_serie)
  REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE
  ON UPDATE RESTRICT ON DELETE RESTRICT;

  CREATE INDEX matricula_ano_idx
  ON pmieducar.matricula
  USING btree
  (ano);

  CREATE INDEX matricula_ativo_idx
  ON pmieducar.matricula
  USING btree
  (ativo);

  VACUUM ANALYZE pmieducar.matricula;
  REINDEX TABLE pmieducar.matricula;


	-- //@UNDO

  ALTER TABLE pmieducar.matricula DROP CONSTRAINT matricula_ref_ref_cod_escola_fkey RESTRICT;
  ALTER TABLE pmieducar.matricula DROP CONSTRAINT matricula_ref_ref_cod_serie_fkey RESTRICT;

  DROP INDEX pmieducar.matricula_ano_idx;
  DROP INDEX pmieducar.matricula_ativo_idx;

	-- //
