  -- //

  --
  -- Cria tabela temporaria que guarda nota do exame dos alunos 
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE TABLE modules.nota_exame
  (
  ref_cod_matricula integer NOT NULL,
  ref_cod_componente_curricular integer NOT NULL,
  nota_exame numeric(5,3),  
  CONSTRAINT moradia_aluno_fkey FOREIGN KEY (ref_cod_matricula)
  REFERENCES pmieducar.matricula(cod_matricula) MATCH SIMPLE
  ON UPDATE RESTRICT ON DELETE RESTRICT
  )
  WITH (
  OIDS=TRUE
  );

  -- //@UNDO

  DROP TABLE modules.nota_exame;

  -- //