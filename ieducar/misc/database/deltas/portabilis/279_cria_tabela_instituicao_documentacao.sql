  -- @author   Paula Bonot <bonot@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

----------------
CREATE SEQUENCE pmieducar.instituicao_documentacao_seq
  INCREMENT 1
  MINVALUE 0
  MAXVALUE 9223372036854775807
  START 2
  CACHE 1;
ALTER TABLE pmieducar.instituicao_documentacao_seq
  OWNER TO ieducar;

----------------
CREATE TABLE pmieducar.instituicao_documentacao
(
  id integer NOT NULL DEFAULT nextval('instituicao_documentacao_seq'::regclass),
  instituicao_id integer NOT NULL,
  titulo_documento  character varying(100) NOT NULL,
  url_documento character varying(255) NOT NULL,
  CONSTRAINT instituicao_documentacao_pkey PRIMARY KEY (id),
  CONSTRAINT instituicao_id_fkey FOREIGN KEY (instituicao_id)
      REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);
ALTER TABLE pmieducar.instituicao_documentacao
  OWNER TO ieducar;
