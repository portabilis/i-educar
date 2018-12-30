CREATE FUNCTION consistenciacao.fcn_unifica_sgsp(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo  ALIAS for $2;
  v_reg      record;
BEGIN
  --Unificando registros das tabelas do SGSP (Sistema de Gererenciamento de Serviços Públicos)--
  SET search_path = servicos, pg_catalog;
  --FUNCIONARIO_AUTORIZADO--
  FOR v_reg IN SELECT *
         FROM funcionario_autorizado
         WHERE
         idpes = v_idpesVelho AND
         idpes <> v_idpesNovo LOOP
    INSERT INTO funcionario_autorizado (idpes, idins) VALUES (v_idpesNovo, v_reg.idins);
  END LOOP;
  --SOLICITACAO_SERVICO--
  UPDATE solicitacao_servico SET idpes_atendente = v_idpesNovo WHERE idpes_atendente = v_idpesVelho;
  UPDATE solicitacao_servico SET idpes_planejamento = v_idpesNovo WHERE idpes_planejamento = v_idpesVelho;
  --SOLICITANTE--
  UPDATE solicitante SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --ORDEM_SERVICO--
  UPDATE ordem_servico SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --APROVACAO--
  UPDATE aprovacao SET idpes_resposta = v_idpesNovo WHERE idpes_resposta = v_idpesVelho;
  --AUTORIZACAO_DIARIA--
  UPDATE autorizacao_diaria SET idpes_usuario = v_idpesNovo WHERE idpes_usuario = v_idpesVelho;
  UPDATE autorizacao_diaria SET idpes_autorizacao = v_idpesNovo WHERE idpes_autorizacao = v_idpesVelho;
  --FUNCIONARIO_AUTORIZADO--
  DELETE FROM funcionario_autorizado WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$;
