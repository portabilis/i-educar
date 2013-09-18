  -- //

  --
  -- Adiciona primary key na tabela ficha_medica_aluno
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  09/2013
	
	ALTER TABLE modules.ficha_medica_aluno ADD CONSTRAINT ficha_medica_cod_aluno_pkey PRIMARY KEY (ref_cod_aluno)
	
  -- //@UNDO

  ALTER TABLE modules.ficha_medica_aluno DROP CONSTRAINT ficha_medica_cod_aluno_pkey;

  -- //