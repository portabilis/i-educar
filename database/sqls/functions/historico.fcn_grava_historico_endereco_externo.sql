CREATE FUNCTION historico.fcn_grava_historico_endereco_externo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_idpes    numeric;
   v_tipo   numeric;
   v_sigla_uf   char(2);
   v_idtlog   varchar;
   v_logradouro   varchar;
   v_numero   numeric;
   v_letra    char(1);
   v_complemento  varchar;
   v_bairro   varchar;
   v_cep    numeric;
   v_cidade   varchar;
   v_reside_desde date;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes    := OLD.idpes;
   v_tipo   := OLD.tipo;
   v_sigla_uf   := OLD.sigla_uf;
   v_idtlog   := OLD.idtlog;
   v_logradouro   := OLD.logradouro;
   v_numero   := OLD.numero;
   v_letra    := OLD.letra;
   v_complemento  := OLD.complemento;
   v_bairro   := OLD.bairro;
   v_cep    := OLD.cep;
   v_cidade   := OLD.cidade;
   v_reside_desde := OLD.reside_desde;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;

  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;

      -- GRAVA HISTÓRICO PARA TABELA ENDERECO_EXTERNO
      INSERT INTO historico.endereco_externo
      (idpes, tipo, sigla_uf, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, reside_desde, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES
      (v_idpes, v_tipo, v_sigla_uf, v_idtlog, v_logradouro, v_numero, v_letra, v_complemento, v_bairro, v_cep, v_cidade, v_reside_desde, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);

   RETURN NEW;

END; $$;
