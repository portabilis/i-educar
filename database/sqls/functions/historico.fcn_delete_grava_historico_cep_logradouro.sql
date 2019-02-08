CREATE FUNCTION historico.fcn_delete_grava_historico_cep_logradouro() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_cep    numeric;
   v_idlog    numeric;
   v_nroini   numeric;
   v_nrofin   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_cep    := OLD.cep;
   v_idlog    := OLD.idlog;
   v_nroini   := OLD.nroini;
   v_nrofin   := OLD.nrofin;
   v_idpes_rev    := OLD.idpes_rev;
   v_data_rev   := OLD.data_rev;
   v_origem_gravacao  := OLD.origem_gravacao;
   v_idsis_rev    := OLD.idsis_rev;
   v_idpes_cad    := OLD.idpes_cad;
   v_data_cad   := OLD.data_cad;
   v_idsis_cad    := OLD.idsis_cad;

  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;

      -- GRAVA HISTÓRICO PARA TABELA CEP_LOGRADOURO
      INSERT INTO historico.cep_logradouro
      (cep, idlog, nroini, nrofin, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES
      (v_cep, v_idlog, v_nroini, v_nrofin, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');

   RETURN NEW;

END; $$;
