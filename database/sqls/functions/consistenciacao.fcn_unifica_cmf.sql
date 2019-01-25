CREATE FUNCTION consistenciacao.fcn_unifica_cmf(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo ALIAS for $2;

BEGIN
  --Unificando registros do SECM (Sistema de Emissao de Certidoes Municipais)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_secm('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGDO (Sistema de Gestao de Despesas Orcamentarias)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgdo('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGP (Sistema de Gerenciamento de Protocolo)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgp('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGPA (Sistema de Gestao da Praca de Atendimento)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgpa('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando as tabelas do sistema de SGSP (Sistema de Gerenciamento de Servicos consistenciacaoos)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sgsp('||v_idpesVelho||','||v_idpesNovo||');';
      --Unificando registros das tabelas do SCD (Sistema de Consintenciacao de Dados)--
   EXECUTE 'SELECT consistenciacao.fcn_unifica_scd('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando registros das tabelas do SCA (Controle de acesso)--
  EXECUTE 'SELECT consistenciacao.fcn_unifica_sca('||v_idpesVelho||','||v_idpesNovo||');';
  --Unificando registros das tabelas de cadastro--
   EXECUTE 'SELECT consistenciacao.fcn_unifica_cadastro('||v_idpesVelho||','||v_idpesNovo||');';
  RETURN 0;
END;$_$;
