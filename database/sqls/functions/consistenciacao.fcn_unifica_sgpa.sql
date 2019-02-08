CREATE FUNCTION consistenciacao.fcn_unifica_sgpa(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho  ALIAS for $1;
  v_idpesNovo   ALIAS for $2;
  v_reg       record;
  v_idAtnVelho  numeric;
  v_idAtnNovo numeric;
BEGIN
  --Unificando as tabelas do sistema de SGPA (Sistema de getao da Praca de Atendimento)--
  SET search_path = praca, pg_catalog;
  --Obtendo o id do atendente velho para poder fazer UPDATE nas tabelas para o id do atendeentente novo--
  FOR v_reg IN SELECT idatn FROM atendente WHERE idpes = v_idpesVelho LOOP
    v_idAtnVelho := v_reg.idatn;
  END LOOP;
  FOR v_reg IN SELECT idatn FROM atendente WHERE idpes = v_idpesNovo LOOP
    v_idAtnNovo := v_reg.idatn;
  END LOOP;
  --ATENDIMENTO--
  UPDATE atendimento SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  UPDATE atendimento SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --TURNO_ATENDENTE--
  FOR v_reg IN SELECT * FROM turno_atendente
         WHERE
         idatn = v_idAtnVelho AND
         idtur NOT IN (SELECT idtur FROM turno_atendente WHERE idatn = v_idAtnNovo) LOOP
    INSERT INTO turno_atendente (idmes,idins,idatn,idtur) VALUES (v_reg.idmes, v_reg.idins, v_idAtnNovo,v_reg.idtur);
  END LOOP;
  DELETE FROM turno_atendente WHERE idatn = v_idAtnVelho;
  --AUSENCIA--
  UPDATE ausencia SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --SITUCAO_ATENDENTE--
  UPDATE situacao_atendente SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --PRODUTIVIDADE--
  UPDATE produtividade SET idatn = v_idAtnNovo WHERE idatn = v_idAtnVelho;
  --ATENDENTE--
  DELETE FROM atendente WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$;
