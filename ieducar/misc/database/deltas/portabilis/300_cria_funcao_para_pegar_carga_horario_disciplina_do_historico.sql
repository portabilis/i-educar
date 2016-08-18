-- Cria função para pegar carga horaria por disciplina no historico
-- @author Paula Bonot <bonot@portabilis.com.br>

CREATE OR REPLACE FUNCTION relatorio.historico_carga_horaria_componente(nm_componente character varying, nm_serie character varying, escola_id integer) RETURNS numeric AS $BODY$
BEGIN
RETURN (SELECT to_number(ccae.carga_horaria,'999')
               FROM modules.componente_curricular_ano_escolar ccae
              INNER JOIN modules.componente_curricular cc ON (upper(translate(cc.nome,'áàâãäåaaaÁÂÃÄÅAAAÀéèêëeeeeeEEEÉEEÈìíîïìiiiÌÍÎÏÌIIIóôõöoooòÒÓÔÕÖOOOùúûüuuuuÙÚÛÜUUUUçÇñÑýÝ','aaaaaaaaaAAAAAAAAAeeeeeeeeeEEEEEEEiiiiiiiiIIIIIIIIooooooooOOOOOOOOuuuuuuuuUUUUUUUUcCnNyY')) = upper(translate(nm_componente,'áàâãäåaaaÁÂÃÄÅAAAÀéèêëeeeeeEEEÉEEÈìíîïìiiiÌÍÎÏÌIIIóôõöoooòÒÓÔÕÖOOOùúûüuuuuÙÚÛÜUUUUçÇñÑýÝ','aaaaaaaaaAAAAAAAAAeeeeeeeeeEEEEEEEiiiiiiiiIIIIIIIIooooooooOOOOOOOOuuuuuuuuUUUUUUUUcCnNyY'))
                                                              AND cc.id = ccae.componente_curricular_id)
              INNER JOIN pmieducar.serie s ON (upper(translate(s.nm_serie,'áàâãäåaaaÁÂÃÄÅAAAÀéèêëeeeeeEEEÉEEÈìíîïìiiiÌÍÎÏÌIIIóôõöoooòÒÓÔÕÖOOOùúûüuuuuÙÚÛÜUUUUçÇñÑýÝ','aaaaaaaaaAAAAAAAAAeeeeeeeeeEEEEEEEiiiiiiiiIIIIIIIIooooooooOOOOOOOOuuuuuuuuUUUUUUUUcCnNyY')) = upper(translate(nm_serie,'áàâãäåaaaÁÂÃÄÅAAAÀéèêëeeeeeEEEÉEEÈìíîïìiiiÌÍÎÏÌIIIóôõöoooòÒÓÔÕÖOOOùúûüuuuuÙÚÛÜUUUUçÇñÑýÝ','aaaaaaaaaAAAAAAAAAeeeeeeeeeEEEEEEEiiiiiiiiIIIIIIIIooooooooOOOOOOOOuuuuuuuuUUUUUUUUcCnNyY'))
                                               AND s.cod_serie = ccae.ano_escolar_id)
               LEFT JOIN pmieducar.escola_serie es ON (es.ref_cod_escola = escola_id
                                                       AND es.ref_cod_serie = s.cod_serie)
ORDER BY ref_cod_escola LIMIT 1);
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


ALTER FUNCTION relatorio.historico_carga_horaria_componente(character varying, character varying, integer) OWNER TO ieducar;

-- Undo

DROP FUNCTION relatorio.historico_carga_horaria_componente(character varying, character varying, integer);