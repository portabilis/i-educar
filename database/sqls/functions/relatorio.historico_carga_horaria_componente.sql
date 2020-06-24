CREATE OR REPLACE FUNCTION relatorio.historico_carga_horaria_componente(nome_componente character varying, nome_serie character varying, escola_id integer) RETURNS numeric
    LANGUAGE plpgsql
AS $$ BEGIN RETURN
    (SELECT to_number(ccae.carga_horaria::varchar,'999')
     FROM modules.componente_curricular_ano_escolar ccae
              INNER JOIN modules.componente_curricular cc ON (fcn_upper(relatorio.get_texto_sem_caracter_especial(cc.nome)) = fcn_upper(relatorio.get_texto_sem_caracter_especial(nome_componente))
         AND cc.id = ccae.componente_curricular_id)
              INNER JOIN pmieducar.serie s ON (fcn_upper(relatorio.get_texto_sem_caracter_especial(s.nm_serie)) = fcn_upper(relatorio.get_texto_sem_caracter_especial(nome_serie))
         AND s.cod_serie = ccae.ano_escolar_id)
              LEFT JOIN pmieducar.escola_serie es ON (es.ref_cod_escola = escola_id
         AND es.ref_cod_serie = s.cod_serie)
     ORDER BY ref_cod_escola LIMIT 1); END; $$;
