CREATE FUNCTION public.cria_distritos() RETURNS void
    LANGUAGE plpgsql
    AS $$
  DECLARE
  cur_log RECORD;
  sequence_val INTEGER;
  begin

    FOR cur_log IN (SELECT idmun, nome, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad
                      FROM public.municipio ORDER BY idmun ASC) LOOP

      INSERT INTO public.distrito (idmun, iddis, nome, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad)
                  VALUES(cur_log.idmun, cur_log.idmun, cur_log.nome, cur_log.idpes_cad, cur_log.data_cad,
                         cur_log.origem_gravacao, cur_log.operacao, cur_log.idsis_cad);
    END LOOP;
    sequence_val := (SELECT max(iddis)+1 FROM public.distrito)::INT;
    PERFORM setval('public.seq_distrito', sequence_val);

  end;$$;
