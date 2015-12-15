--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE OR REPLACE FUNCTION relatorio.get_valor_campo_auditoria(character varying, character varying, character varying) RETURNS character varying AS $BODY$
SELECT CASE
           WHEN $2 = '' THEN substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, '}')) - (strpos($3, $1)+char_length($1)+1)))
           ELSE substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, $2||':')-1) - (strpos($3, $1)+char_length($1)+1)))
       END AS nome_instituicao;$BODY$ LANGUAGE SQL VOLATILE;

ALTER FUNCTION relatorio.get_valor_campo_auditoria(character varying, character varying, character varying) OWNER TO ieducar;

 -- undo

CREATE OR REPLACE FUNCTION relatorio.get_valor_campo_auditoria(character varying, character varying, character varying) RETURNS character varying AS $BODY$
SELECT CASE
           WHEN $2 = '' THEN substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, '}')) - (strpos($3, $1)+char_length($1)+1)))
           ELSE substr($3, strpos($3, $1||':')+char_length($1)+1, ((strpos($3, $2)-1) - (strpos($3, $1)+char_length($1)+1)))
       END AS nome_instituicao;$BODY$ LANGUAGE SQL VOLATILE;

ALTER FUNCTION relatorio.get_valor_campo_auditoria(character varying, character varying, character varying) OWNER TO ieducar;

