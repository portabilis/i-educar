  -- //
  -- Essa migração cria tabela de vínculo entre alunos e projetos
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE TABLE pmieducar.projeto_aluno
  (
    ref_cod_projeto INTEGER NOT NULL,
    ref_cod_aluno INTEGER NOT NULL,
    data_inclusao DATE,
    data_desligamento DATE,
    turno INTEGER,
    CONSTRAINT pmieducar_projeto_aluno_pk PRIMARY KEY (ref_cod_projeto, ref_cod_aluno),
    CONSTRAINT pmieducar_projeto_aluno_ref_cod_projeto FOREIGN KEY (ref_cod_projeto)
    REFERENCES pmieducar.projeto (cod_projeto) MATCH SIMPLE,
    CONSTRAINT pmieducar_projeto_aluno_ref_cod_aluno FOREIGN KEY (ref_cod_aluno)
    REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE
  )
  WITH (
    OIDS=TRUE
  );

  -- //@UNDO

  DROP TABLE pmieducar.projeto_aluno;

  -- //