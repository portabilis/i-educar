CREATE OR REPLACE FUNCTION public.unifica_bairro(p_idbai_duplicado integer, p_idbai_principal integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
cur_cep_log_bai RECORD;
begin

FOR cur_cep_log_bai IN (SELECT * FROM urbano.cep_logradouro_bairro clb WHERE clb.idbai = p_idbai_duplicado) LOOP

IF (SELECT 1 FROM urbano.cep_logradouro_bairro clb
      WHERE clb.idlog = cur_cep_log_bai.idlog
      AND clb.cep = cur_cep_log_bai.cep
      AND clb.idbai = p_idbai_principal
      LIMIT 1) IS NULL THEN

  INSERT INTO urbano.cep_logradouro_bairro (idlog, cep, idbai, origem_gravacao, idpes_cad, data_cad, operacao)
                             VALUES (cur_cep_log_bai.idlog, cur_cep_log_bai.cep, p_idbai_principal, 'U', 1, NOW(), 'I');


END IF;
END LOOP;

UPDATE cadastro.endereco_pessoa SET idbai = p_idbai_principal WHERE idbai = p_idbai_duplicado;
DELETE FROM urbano.cep_logradouro_bairro WHERE idbai = p_idbai_duplicado;
DELETE FROM public.bairro WHERE idbai = p_idbai_duplicado;

end;$$;
