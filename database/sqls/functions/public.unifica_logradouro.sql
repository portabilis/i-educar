CREATE OR REPLACE FUNCTION public.unifica_logradouro(p_idlog_duplicado integer, p_idlog_principal integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
cur_cep_log RECORD;
begin

FOR cur_cep_log IN (SELECT * FROM urbano.cep_logradouro_bairro clb WHERE clb.idlog = p_idlog_duplicado) LOOP

IF (SELECT 1 FROM urbano.cep_logradouro cl
      WHERE cl.idlog = p_idlog_principal
      AND cl.cep = cur_cep_log.cep
      LIMIT 1) IS NULL THEN

  INSERT INTO urbano.cep_logradouro (idlog, cep, origem_gravacao, idpes_cad, data_cad, operacao)
                             VALUES (p_idlog_principal, cur_cep_log.cep, 'U', 1, NOW(), 'I');


END IF;

IF (SELECT 1 FROM urbano.cep_logradouro_bairro clb
      WHERE clb.idlog = p_idlog_principal
      AND clb.cep = cur_cep_log.cep
      AND clb.idbai = cur_cep_log.idbai
      LIMIT 1) IS NULL THEN

  INSERT INTO urbano.cep_logradouro_bairro (idlog, cep, idbai, origem_gravacao, idpes_cad, data_cad, operacao)
                             VALUES (p_idlog_principal, cur_cep_log.cep, cur_cep_log.idbai, 'U', 1, NOW(), 'I');


END IF;
END LOOP;

UPDATE cadastro.endereco_pessoa SET idlog = p_idlog_principal WHERE idlog = p_idlog_duplicado;
DELETE FROM urbano.cep_logradouro_bairro WHERE idlog = p_idlog_duplicado;
DELETE FROM urbano.cep_logradouro WHERE idlog = p_idlog_duplicado;
DELETE FROM public.logradouro WHERE idlog = p_idlog_duplicado;

end;$$;
