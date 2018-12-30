CREATE FUNCTION public.fcn_delete_funcionario(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_matricula ALIAS for $1;
  v_id_ins ALIAS for $2;

BEGIN
  -- Deleta dados da tabela funcionário
  DELETE FROM cadastro.funcionario WHERE matricula = v_matricula AND idins = v_id_ins;
  RETURN 0;
END;$_$;
