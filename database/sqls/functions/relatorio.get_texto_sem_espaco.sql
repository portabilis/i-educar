CREATE OR REPLACE FUNCTION relatorio.get_texto_sem_espaco(character varying) RETURNS character varying
    LANGUAGE sql
    AS $_$
SELECT translate(public.fcn_upper(regexp_replace($1,' ','','g')), 'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ', 'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN');$_$;
