  -- //

  --
  -- Cria tabela para uniforme escolar do aluno
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  ?
  
  CREATE TABLE modules.uniforme_aluno
  (
  ref_cod_aluno integer NOT NULL,
  recebeu_uniforme character(1),
  quantidade_camiseta integer,
  tamanho_camiseta character(2),
  quantidade_blusa_jaqueta integer,
  tamanho_blusa_jaqueta character(2),
  quantidade_bermuda integer,
  tamanho_bermuda character(2),
  quantidade_calca integer,
  tamanho_calca character(2),
  quantidade_saia integer,
  tamanho_saia character(2),
  quantidade_calcado integer,
  tamanho_calcado character(2),
  quantidade_meia integer,
  tamanho_meia character(2),
  CONSTRAINT uniforme_aluno_pkey PRIMARY KEY (ref_cod_aluno),
  CONSTRAINT uniforme_aluno_fkey FOREIGN KEY (ref_cod_aluno)
  REFERENCES pmieducar.aluno(cod_aluno) MATCH SIMPLE
  ON UPDATE RESTRICT ON DELETE RESTRICT
  )
  WITH (
  OIDS=TRUE
  );
  
  -- //@UNDO

  DROP TABLE modules.uniforme_aluno

  -- //