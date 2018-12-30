CREATE FUNCTION public.fcn_delete_fone_pessoa(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;

BEGIN
  -- Deleta dados da tabela fone_pessoa
  DELETE FROM cadastro.fone_pessoa WHERE idpes = v_id_pes;
  RETURN 0;
END;$_$;

