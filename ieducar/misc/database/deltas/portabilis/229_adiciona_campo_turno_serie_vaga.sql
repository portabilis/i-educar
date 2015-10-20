--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.serie_vaga ADD COLUMN turno SMALLINT NOT NULL DEFAULT 1;
ALTER TABLE pmieducar.serie_vaga DROP CONSTRAINT cod_serie_vaga_unique;
ALTER TABLE pmieducar.serie_vaga ADD CONSTRAINT cod_serie_vaga_unique UNIQUE (ano, ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, turno);