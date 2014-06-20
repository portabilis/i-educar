  -- //

  --
  -- Cria campo tabela para vínculos de benefícios com alunos
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
  CREATE TABLE pmieducar.aluno_aluno_beneficio
  (
    aluno_id INTEGER NOT NULL,
    aluno_beneficio_id INTEGER NOT NULL,
    CONSTRAINT aluno_aluno_beneficio_pk PRIMARY KEY (aluno_id, aluno_beneficio_id),
    CONSTRAINT aluno_aluno_beneficio_aluno_fk FOREIGN KEY (aluno_id)
    REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE,
    CONSTRAINT aluno_aluno_beneficio_aluno_beneficio_fk FOREIGN KEY (aluno_beneficio_id)
    REFERENCES pmieducar.aluno_beneficio (cod_aluno_beneficio) MATCH SIMPLE
  );

  CREATE OR REPLACE FUNCTION pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio()
    RETURNS void AS
  $BODY$
  DECLARE 
  cur_aluno RECORD;
  begin           

  FOR cur_aluno IN (SELECT cod_aluno as id, ref_cod_aluno_beneficio as beneficio_id FROM pmieducar.aluno WHERE ref_cod_aluno_beneficio IS NOT NULL) LOOP
    INSERT INTO pmieducar.aluno_aluno_beneficio VALUES (cur_aluno.id, cur_aluno.beneficio_id);
  END LOOP;

  ALTER TABLE pmieducar.aluno DROP COLUMN ref_cod_aluno_beneficio;
  end;$BODY$
    LANGUAGE 'plpgsql' VOLATILE;

  SELECT pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio();


  -- //@UNDO
 
  DROP TABLE pmieducar.aluno_aluno_beneficio;
  DROP FUNCTION pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio();

  -- //