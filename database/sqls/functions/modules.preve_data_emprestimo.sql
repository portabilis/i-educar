CREATE OR REPLACE FUNCTION modules.preve_data_emprestimo(biblioteca_id integer, data_prevista date) RETURNS date
    LANGUAGE plpgsql
    AS $$
  DECLARE
  begin

  IF (( select 1 from pmieducar.biblioteca_dia WHERE ref_cod_biblioteca = biblioteca_id AND dia = ((SELECT EXTRACT(DOW FROM data_prevista))+1) limit 1) IS NOT null) THEN
    IF ((SELECT 1 FROM pmieducar.biblioteca_feriados WHERE ref_cod_biblioteca = biblioteca_id and data_feriado = data_prevista) IS NULL) THEN
      RETURN data_prevista;
    ELSE
      RETURN modules.preve_data_emprestimo(biblioteca_id, data_prevista+1);
    END IF;
  ELSE
    RETURN modules.preve_data_emprestimo(biblioteca_id, data_prevista+1);
  END IF;

  end;$$;
