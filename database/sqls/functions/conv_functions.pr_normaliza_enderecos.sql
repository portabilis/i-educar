CREATE FUNCTION conv_functions.pr_normaliza_enderecos() RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
cur 	record;
i_counter integer;
i_idmun integer;
i_iddis integer;
i_idbai integer;
i_idlog integer;

BEGIN

i_counter := 0;

for cur in SELECT
      ee.idpes,
      ee.idtlog,
      ee.logradouro,
      ee.numero,
      ee.letra,
      ee.complemento,
      ee.bairro,
      ee.cep,
      ee.bloco,
      ee.andar,
      ee.cidade,
      ee.zona_localizacao,
      upper(ee.sigla_uf) as sigla_uf

    FROM cadastro.pessoa p
    LEFT JOIN cadastro.endereco_pessoa ep
      ON ep.idpes = p.idpes
    LEFT JOIN cadastro.endereco_externo ee
      ON ee.idpes = p.idpes
    WHERE ep.idpes is null
    and ee.idpes is not null loop

  i_counter := i_counter + 1;

  i_idmun := 0;
  i_iddis := 0;
  i_idbai := 0;
  i_idlog := 0;

  raise notice 'counter: %', i_counter;

  SELECT COALESCE((select m.idmun
            from public.municipio m
            WHERE unaccent(nome) ilike unaccent(cur.cidade)
            AND sigla_uf = cur.sigla_uf
            LIMIT 1),0) INTO i_idmun;

  If i_idmun = 0 then
    INSERT INTO public.municipio (nome, sigla_uf, tipo, idpes_cad, data_cad, origem_gravacao, operacao, idsis_cad)
      VALUES (cur.cidade, cur.sigla_uf, 'M', 1, NOW(), 'M', 'I', 9) RETURNING idmun INTO i_idmun ;
  End if;

  SELECT COALESCE((SELECT b.idbai
              FROM public.bairro b
              WHERE trim(unaccent(nome)) ilike trim(unaccent(cur.bairro))
              AND b.idmun = i_idmun
              LIMIT 1),0) INTO i_idbai;

  If i_idbai = 0 then
    SELECT COALESCE((SELECT d.iddis
                  FROM public.distrito d
                  WHERE d.idmun = i_idmun
                  LIMIT 1),0) INTO i_iddis;


    If i_iddis = 0 then
      INSERT INTO public.distrito (idmun, nome, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
        VALUES (i_idmun, cur.cidade, 'M', 1, now(), 'I', 1) returning iddis INTO i_iddis;
    End if;

    INSERT INTO public.bairro (idmun, iddis, nome, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad, zona_localizacao)
      VALUES (i_idmun, i_iddis, cur.bairro, 'M', 1, NOW(), 'I', 1, cur.zona_localizacao) RETURNING idbai INTO i_idbai;
  End if;

  SELECT COALESCE((SELECT idlog
    FROM public.logradouro
    WHERE idmun = i_idmun
    AND trim(unaccent(nome)) ILIKE trim(unaccent(cur.logradouro))
    LIMIT 1
    ),0) INTO i_idlog;

  If i_idlog = 0 THEN
    INSERT INTO public.logradouro (idtlog, nome, idmun, ident_oficial, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
    VALUES (cur.idtlog, cur.logradouro, i_idmun, 'S', 'M', 1, NOW(), 'I', 1) RETURNING idlog INTO i_idlog;

  End if;

  If NOT EXISTS (SELECT 1
              FROM urbano.cep_logradouro
              WHERE cep = cur.cep
              AND idlog = i_idlog) THEN
    INSERT INTO urbano.cep_logradouro (cep, idlog, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
      VALUES (cur.cep, i_idlog, 'M', 1, NOW(), 'I', 1);
  END if;

  If NOT EXISTS (SELECT 1
              FROM urbano.cep_logradouro_bairro
              WHERE cep = cur.cep
              AND idlog = i_idlog
              AND idbai = i_idbai
              ) THEN
    INSERT INTO urbano.cep_logradouro_bairro (cep, idlog, idbai, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
      VALUES (cur.cep, i_idlog, i_idbai, 'M', 1, NOW(), 'I', 1);
  END if;

  INSERT INTO cadastro.endereco_pessoa (idpes, tipo, cep, idlog, numero, letra, complemento, idbai, bloco, andar,  origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
    VALUES (cur.idpes, 1, cur.cep, i_idlog, cur.numero, cur.letra, cur.complemento, i_idbai, cur.bloco, cur.andar, 'M', 1, NOW(), 'I', 1);

end loop;

end;
$$;
