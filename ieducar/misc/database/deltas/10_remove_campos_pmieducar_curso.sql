-- //

--
-- Remove campos da tabela pmieducar.curso que ficarão em desuso após integração 
-- com módulo Regra Avaliação.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

DROP INDEX pmieducar.i_curso_edicao_final;
DROP INDEX pmieducar.i_curso_falta_ch_globalizada;
DROP INDEX pmieducar.i_curso_ref_cod_tipo_avaliacao;

ALTER TABLE pmieducar.curso
  DROP CONSTRAINT curso_ref_cod_tipo_avaliacao_fkey;

ALTER TABLE pmieducar.curso DROP COLUMN ref_cod_tipo_avaliacao;
ALTER TABLE pmieducar.curso DROP COLUMN frequencia_minima;
ALTER TABLE pmieducar.curso DROP COLUMN media;
ALTER TABLE pmieducar.curso DROP COLUMN media_exame;
ALTER TABLE pmieducar.curso DROP COLUMN falta_ch_globalizada;
ALTER TABLE pmieducar.curso DROP COLUMN edicao_final;
ALTER TABLE pmieducar.curso DROP COLUMN avaliacao_globalizada;

-- //@UNDO

ALTER TABLE pmieducar.curso ADD COLUMN ref_cod_tipo_avaliacao integer;
ALTER TABLE pmieducar.curso ADD COLUMN frequencia_minima double precision NOT NULL DEFAULT 0.00;
ALTER TABLE pmieducar.curso ADD COLUMN media double precision NOT NULL DEFAULT 0.00;
ALTER TABLE pmieducar.curso ADD COLUMN media_exame double precision;
ALTER TABLE pmieducar.curso ADD COLUMN falta_ch_globalizada smallint NOT NULL DEFAULT (0);
ALTER TABLE pmieducar.curso ADD COLUMN edicao_final smallint NOT NULL DEFAULT (0);
ALTER TABLE pmieducar.curso ADD COLUMN avaliacao_globalizada boolean NOT NULL DEFAULT false;

CREATE INDEX i_curso_edicao_final
  ON pmieducar.curso
  USING btree
  (edicao_final);

CREATE INDEX i_curso_falta_ch_globalizada
  ON pmieducar.curso
  USING btree
  (falta_ch_globalizada);

CREATE INDEX i_curso_ref_cod_tipo_avaliacao
  ON pmieducar.curso
  USING btree
  (ref_cod_tipo_avaliacao);

ALTER TABLE pmieducar.curso
  ADD CONSTRAINT curso_ref_cod_tipo_avaliacao_fkey 
  FOREIGN KEY (ref_cod_tipo_avaliacao)
  REFERENCES pmieducar.tipo_avaliacao (cod_tipo_avaliacao) MATCH SIMPLE
  ON UPDATE RESTRICT 
  ON DELETE RESTRICT;

-- //