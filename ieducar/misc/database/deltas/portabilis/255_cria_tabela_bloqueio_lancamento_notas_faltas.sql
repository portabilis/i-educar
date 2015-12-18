
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

-- Cria sequencial
CREATE SEQUENCE bloqueio_lancamento_faltas_notas_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    MINVALUE 0
    CACHE 1;

SELECT pg_catalog.setval('bloqueio_lancamento_faltas_notas_seq', 1, false);
SET default_with_oids = true;

-- Cria tabela bloqueio_lancamento_faltas_notas
CREATE TABLE pmieducar.bloqueio_lancamento_faltas_notas
(
  cod_bloqueio integer NOT NULL DEFAULT nextval('bloqueio_lancamento_faltas_notas_seq'::regclass),
  ano integer NOT NULL,
  ref_cod_escola integer NOT NULL,
  etapa integer NOT NULL,
  data_inicio date NOT NULL,
  data_fim date NOT NULL,
  CONSTRAINT fk_bloqueio_lancamento_faltas_notas PRIMARY KEY (cod_bloqueio),
  CONSTRAINT bloqueio_lancamento_faltas_notas_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);
ALTER TABLE pmieducar.bloqueio_lancamento_faltas_notas
  OWNER TO ieducar;

-- undo

DROP TABLE pmieducar.bloqueio_lancamento_faltas_notas;
DROP SEQUENCE bloqueio_lancamento_faltas_notas_seq;