CREATE FUNCTION historico.fcn_grava_historico_pessoa() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_idpes    numeric;
   v_nome   TEXT;
   v_idpes_cad    numeric;
   v_data_cad   timestamp;
   v_url    TEXT;
   v_tipo   char;
   v_idpes_rev    numeric;
   v_data_rev   timestamp;
   v_email    TEXT;
   v_situacao   char;
   v_origem_gravacao  char;
   v_idsis_rev    numeric;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes    := OLD.idpes;
   v_nome   := OLD.nome;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_url    := OLD.url;
   v_tipo   := OLD.tipo;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_email    := OLD.email;
   v_situacao   := OLD.situacao;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idsis_cad    := OLD.idsis_cad;
   v_operacao   := OLD.operacao;

  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;

      -- GRAVA HISTÓRICO PARA TABELA PESSOA
      INSERT INTO historico.pessoa
      (idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, email, situacao, origem_gravacao, idsis_rev, idsis_cad, operacao) VALUES
      (v_idpes, v_nome, v_idpes_cad, v_data_cad, v_url, v_tipo, v_idpes_rev, v_data_rev, v_email, v_situacao, v_origem_gravacao, v_idsis_rev, v_idsis_cad, v_operacao);

   RETURN NEW;

END; $$;
