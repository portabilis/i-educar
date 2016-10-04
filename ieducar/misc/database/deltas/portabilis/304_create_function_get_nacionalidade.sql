-- Cria function que retorna a nacionalidade da pessoa fisica
-- @author Caroline Salib <caroline@portabilis.com.br>

CREATE OR REPLACE FUNCTION relatorio.get_nacionalidade(nacionalidade_id numeric) RETURNS VARCHAR AS $$ BEGIN RETURN
  (SELECT CASE
              WHEN nacionalidade_id = 1 THEN 'Brasileiro'
              WHEN nacionalidade_id = 2 THEN 'Naturalizado Brasileiro'
              ELSE 'Estrangeiro'
          END); END; $$ LANGUAGE plpgsql;

 -- @undo

DROP FUNCTION IF EXISTS relatorio.get_nacionalidade(nacionalidade_id numeric);