CREATE FUNCTION consistenciacao.fcn_unifica_sca(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  --Parametro recebidos--
  v_idpesVelho ALIAS for $1;
  v_idpesNovo  ALIAS for $2;
  v_loginAux   varchar;
  v_reg      record;
BEGIN
  --Unificando registros das tabelas do SCA (Sistema de Controle de Acesso)--
  SET search_path = acesso, pg_catalog;
  --PESSOA_INSTITUICAO--
  --Inserindo as instituicoes do idvelho para o id novo--
  FOR v_reg IN SELECT *
               FROM pessoa_instituicao
               WHERE
         idpes = v_idpesVelho AND
         idins NOT IN
        (SELECT idins FROM
         pessoa_instituicao
         WHERE idpes = v_idpesNovo) LOOP
    INSERT INTO pessoa_instituicao (idpes,idins) VALUES (v_idpesNovo, v_reg.idins);
  END LOOP;
  --Apagando os registro do id velho--
  EXECUTE 'DELETE FROM pessoa_instituicao WHERE idpes = '||v_idpesVelho||';';
  --LOG_ACESSO--
  EXECUTE 'UPDATE log_acesso SET idpes ='||v_idpesNovo||' WHERE idpes = '||v_idpesVelho||';';
  --USUARIO_GRUPO--
  --Obtendo o login do novo idpes para ser usado na unificacao da tabela usuario_grupo--
  FOR v_reg IN SELECT login FROM usuario WHERE idpes = v_idpesNovo LOOP
    v_loginAux := v_reg.login;
  END LOOP;
  --Inserindo os grupos do idpes velho para o idpes novo--
  FOR v_reg IN SELECT *
         FROM usuario_grupo
         WHERE
         login IN (SELECT login FROM usuario WHERE idpes = v_idpesVelho) AND
         idgrp NOT IN (SELECT idgrp
                       FROM usuario_grupo
           WHERE
           login = v_loginAux) LOOP
    INSERT INTO usuario_grupo (idgrp,login) VALUES (v_reg.idgrp, v_loginAux);
  END LOOP;
  --Deletando os registros do idpes velho--
  EXECUTE 'DELETE FROM usuario_grupo
            WHERE login IN
         (SELECT login FROM usuario WHERE idpes ='|| v_idpesVelho ||');';
  --LOG_ERRO--
  EXECUTE 'UPDATE log_erro SET idpes = '||v_idpesNovo||' WHERE idpes = '||v_idpesVelho||';';

  --USUARIO--
  EXECUTE 'DELETE FROM usuario WHERE idpes ='|| v_idpesVelho||';';
  RETURN 0;
END;$_$;
