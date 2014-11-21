  -- //

  --
  -- Função que unifica bairro ao passar bairro duplicado e bairro principal por parãmetro
  -- Função que unifica logradouro ao passar logradouro duplicado e logradouro principal por parãmetro
  -- Insere menus
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  INSERT INTO portal.menu_submenu VALUES (761, 68, 2, 'Unificação de bairros', 'educar_unifica_bairro.php', null, 3);
  INSERT INTO portal.menu_submenu VALUES (762, 68, 2, 'Unificação de logradouros', 'educar_unifica_logradouro.php', null, 3);

  CREATE OR REPLACE FUNCTION public.unifica_bairro(p_idbai_duplicado integer, p_idbai_principal integer)
  RETURNS void AS
  $BODY$
  DECLARE 
  cur_cep_log_bai RECORD;
  begin 

  FOR cur_cep_log_bai IN (SELECT * FROM urbano.cep_logradouro_bairro clb WHERE clb.idbai = p_idbai_duplicado) LOOP

    IF (SELECT 1 FROM urbano.cep_logradouro_bairro clb 
          WHERE clb.idlog = cur_cep_log_bai.idlog 
          AND clb.cep = cur_cep_log_bai.cep
          AND clb.idbai = p_idbai_principal
          LIMIT 1) IS NULL THEN

      INSERT INTO urbano.cep_logradouro_bairro (idlog, cep, idbai, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
                                 VALUES (cur_cep_log_bai.idlog, cur_cep_log_bai.cep, p_idbai_principal, 'U', 1, NOW(), 'I', 9);


    END IF;
  END LOOP;

  UPDATE cadastro.endereco_pessoa SET idbai = p_idbai_principal WHERE idbai = p_idbai_duplicado;
  DELETE FROM urbano.cep_logradouro_bairro WHERE idbai = p_idbai_duplicado;
  DELETE FROM public.bairro WHERE idbai = p_idbai_duplicado;

  end;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

  CREATE OR REPLACE FUNCTION public.unifica_logradouro(p_idlog_duplicado integer, p_idlog_principal integer)
  RETURNS void AS
  $BODY$
  DECLARE 
  cur_cep_log RECORD;
  begin 

  FOR cur_cep_log IN (SELECT * FROM urbano.cep_logradouro_bairro clb WHERE clb.idlog = p_idlog_duplicado) LOOP

    IF (SELECT 1 FROM urbano.cep_logradouro cl 
          WHERE cl.idlog = p_idlog_principal
          AND cl.cep = cur_cep_log.cep
          LIMIT 1) IS NULL THEN

      INSERT INTO urbano.cep_logradouro (idlog, cep, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
                                 VALUES (p_idlog_principal, cur_cep_log.cep, 'U', 1, NOW(), 'I', 9);


    END IF;

    IF (SELECT 1 FROM urbano.cep_logradouro_bairro clb 
          WHERE clb.idlog = p_idlog_principal 
          AND clb.cep = cur_cep_log.cep
          AND clb.idbai = cur_cep_log.idbai
          LIMIT 1) IS NULL THEN

      INSERT INTO urbano.cep_logradouro_bairro (idlog, cep, idbai, origem_gravacao, idpes_cad, data_cad, operacao, idsis_cad)
                                 VALUES (p_idlog_principal, cur_cep_log.cep, cur_cep_log.idbai, 'U', 1, NOW(), 'I', 9);


    END IF;
  END LOOP;

  UPDATE cadastro.endereco_pessoa SET idlog = p_idlog_principal WHERE idlog = p_idlog_duplicado;
  DELETE FROM urbano.cep_logradouro_bairro WHERE idlog = p_idlog_duplicado;
  DELETE FROM urbano.cep_logradouro WHERE idlog = p_idlog_duplicado;
  DELETE FROM public.logradouro WHERE idlog = p_idlog_duplicado;

  end;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;  

  -- //