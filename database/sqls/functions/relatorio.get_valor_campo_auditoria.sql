CREATE OR REPLACE FUNCTION relatorio.get_valor_campo_auditoria(character varying, character varying, character varying) RETURNS character varying
    LANGUAGE sql
AS $_$
SELECT CASE
           WHEN $2 = '' THEN substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, '}')) - (strpos($3, $1)+char_length($1)+1)))
           ELSE substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, $2||':')-1) - (strpos($3, $1)+char_length($1)+1)))
           END AS nome_instituicao;$_$;
