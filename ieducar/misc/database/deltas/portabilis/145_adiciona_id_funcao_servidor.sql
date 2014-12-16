  -- //

  --
  -- Altera estrutura de servidores para permitir mais que uma funcao igual no mesmo servidor
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE SEQUENCE pmieducar.servidor_funcao_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

  ALTER TABLE pmieducar.servidor_funcao ADD COLUMN cod_servidor_funcao INTEGER NOT NULL DEFAULT nextval('pmieducar.servidor_funcao_seq'::regclass);

  ALTER TABLE pmieducar.servidor_funcao DROP CONSTRAINT servidor_funcao_pkey;

  ALTER TABLE pmieducar.servidor_funcao ADD CONSTRAINT cod_servidor_funcao_pkey PRIMARY KEY (cod_servidor_funcao);

  -- //