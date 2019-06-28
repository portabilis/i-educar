CREATE OR REPLACE FUNCTION relatorio.get_texto_sem_caracter_especial(character varying) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT translate(public.fcn_upper($1),
                       'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ',
                       'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN');$_$;
