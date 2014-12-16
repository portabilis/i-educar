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

  ALTER TABLE pmieducar.servidor_alocacao ADD COLUMN ref_cod_servidor_funcao INTEGER;

  UPDATE pmieducar.servidor_alocacao sa SET ref_cod_servidor_funcao = (SELECT sf.cod_servidor_funcao FROM pmieducar.servidor_funcao sf WHERE sa.ref_ref_cod_instituicao = sf.ref_ref_cod_instituicao AND sa.ref_cod_servidor = sf.ref_cod_servidor LIMIT 1);

  ALTER TABLE pmieducar.servidor_alocacao DROP COLUMN ref_cod_funcao;

  -- //