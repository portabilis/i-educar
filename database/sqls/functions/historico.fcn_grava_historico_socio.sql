CREATE FUNCTION historico.fcn_grava_historico_socio() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_idpes_juridica   numeric;
   v_idpes_fisica   numeric;
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
   v_operacao     char(1);
BEGIN
   v_idpes_juridica   := OLD.idpes_juridica;
   v_idpes_fisica   := OLD.idpes_fisica;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;

  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;

      -- GRAVA HISTÓRICO PARA TABELA SOCIO
      INSERT INTO historico.socio
      (idpes_juridica, idpes_fisica, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES
      (v_idpes_juridica, v_idpes_fisica, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);

   RETURN NEW;

END; $$;
