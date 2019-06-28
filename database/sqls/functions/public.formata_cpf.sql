CREATE OR REPLACE FUNCTION public.formata_cpf(cpf numeric) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
DECLARE
	cpf_formatado varchar := '';
BEGIN
  cpf_formatado := (SUBSTR(TO_CHAR(cpf, '00000000000'), 1, 4) || '.' ||
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 5, 3) || '.' ||
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 8, 3) || '-' ||
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 11, 2)) ;
  RETURN cpf_formatado;
END;
$$;
