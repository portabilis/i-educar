CREATE FUNCTION consistenciacao.fcn_unifica_cadastro(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho     ALIAS for $1;
  v_idpesNovo      ALIAS for $2;
  v_cpfAux           NUMERIC;
  v_cpfIdpesNovo     NUMERIC;
  v_reg          record;
  v_regAux         record;
BEGIN
  --Unificando registros das tabelas do cadastros de pessoas--
  SET search_path = cadastro, pg_catalog;
  --FONE_PESSOA--
  FOR v_reg IN SELECT *
       FROM fone_pessoa
       WHERE
       idpes = v_idpesVelho AND
       tipo NOT IN (SELECT tipo FROM fone_pessoa WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO fone_pessoa (idpes, tipo, ddd, fone) VALUES (v_idpesNovo,v_reg.tipo, v_reg.ddd, v_reg.fone);
  END LOOP;
  --Atualizando os telefones do idpeNovo que tenham o DDD e o FONE igual a zero--
  FOR v_reg IN SELECT * FROM fone_pessoa WHERE idpes = v_idpesNovo AND ddd = 0 AND fone = 0 LOOP
    DELETE FROM fone_pessoa WHERE idpes = v_reg.idpes AND tipo = v_reg.tipo;
    FOR v_regAux IN SELECT * FROM fone_pessoa WHERE idpes = v_idpesVelho AND tipo = v_reg.tipo LOOP
      INSERT INTO fone_pessoa(idpes,tipo,ddd,fone) VALUES (v_reg.idpes,v_regAux.tipo,v_regAux.ddd,v_regAux.fone);
    END LOOP;
  END LOOP;
  DELETE FROM fone_pessoa WHERE idpes = v_idpesVelho;
  --JURIDICA--
  DELETE FROM juridica WHERE idpes = v_idpesVelho;
  --HISTORICO_CARTAO--
  UPDATE historico_cartao SET idpes_cidadao = v_idpesNovo WHERE idpes_cidadao = v_idpesVelho;
  --ENDERECO_PESSOA--
  FOR v_reg IN SELECT *
         FROM endereco_pessoa
         WHERE
         idpes = v_idpesVelho AND
         tipo NOT IN (SELECT tipo FROM endereco_pessoa WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO endereco_pessoa (idpes, tipo, cep, idlog, idbai, numero, letra, complemento, reside_desde)
    VALUES (v_idpesNovo,v_reg.tipo, v_reg.cep, v_reg.idlog, v_reg.idbai, v_reg.numero, v_reg.letra, v_reg.complemento, v_reg.reside_desde);
  END LOOP;
  DELETE FROM endereco_pessoa WHERE idpes = v_idpesVelho;
  --ENDERECO_EXTERNO--
  FOR v_reg IN SELECT *
     FROM endereco_externo
     WHERE
     idpes = v_idpesVelho AND
     tipo NOT IN (SELECT tipo FROM endereco_externo WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO endereco_externo (idpes, tipo, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, sigla_uf,reside_desde)
    VALUES (v_idpesNovo, v_reg.tipo, v_reg.idtlog, v_reg.logradouro, v_reg.numero, v_reg.letra, v_reg.complemento, v_reg.bairro, v_reg.cep, v_reg.cidade, v_reg.sigla_uf,v_reg.reside_desde);
  END LOOP;
  DELETE FROM endereco_externo WHERE idpes = v_idpesVelho;
  --FISICA_CPF--
  --Obtendo enventual CPF da pessoa antiga para ser inserido na pessoa nova--
  FOR v_reg IN SELECT cpf FROM fisica_cpf WHERE idpes = v_idpesVelho AND idpes <> v_idpesNovo LOOP
    v_cpfAux := v_reg.cpf;
  END LOOP;
  DELETE FROM fisica_cpf WHERE idpes = v_idpesVelho;
  IF v_cpfAux IS NOT NULL THEN
    FOR v_reg IN SELECT cpf FROM fisica_cpf WHERE idpes = v_idpesNovo LOOP
      v_cpfIdpesNovo := v_reg.cpf;
    END LOOP;
    --Verificando se o idpess já possuí um CPF--
    IF v_cpfIdpesNovo IS NULL THEN
      INSERT INTO fisica_cpf (idpes, cpf) VALUES (v_idpesNovo,v_cpfAux);
    END IF;
  END IF;
  --FUNCIONARIO--
  UPDATE funcionario SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --DOCUMENTO--
  DELETE FROM documento WHERE idpes = v_idpesVelho;
  --FISICA--
  DELETE FROM fisica WHERE idpes = v_idpesVelho;
  --PESSOA--
  DELETE FROM pessoa WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$;
