CREATE OR REPLACE FUNCTION pmieducar.migra_beneficios_para_tabela_aluno_aluno_beneficio() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_aluno RECORD;
  begin

  FOR cur_aluno IN (SELECT cod_aluno as id, ref_cod_aluno_beneficio as beneficio_id FROM pmieducar.aluno WHERE ref_cod_aluno_beneficio IS NOT NULL) LOOP
    INSERT INTO pmieducar.aluno_aluno_beneficio VALUES (cur_aluno.id, cur_aluno.beneficio_id);
  END LOOP;

  ALTER TABLE pmieducar.aluno DROP COLUMN ref_cod_aluno_beneficio;
  end;$$;
