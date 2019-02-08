CREATE FUNCTION historico.fcn_delete_grava_historico_funcionario() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_matricula      numeric;
   v_idins      numeric;
   v_idset      numeric;
   v_idpes      numeric;
   v_situacao     char(1);
   v_idpes_rev      numeric;
   v_data_rev     TIMESTAMP;
   v_origem_gravacao    char(1);
   v_idsis_rev      numeric;
   v_idpes_cad      numeric;
   v_data_cad     TIMESTAMP;
   v_idsis_cad      numeric;
BEGIN
   v_matricula      := OLD.matricula;
   v_idins      := OLD.idins;
   v_idset      := OLD.idset;
   v_idpes      := OLD.idpes;
   v_situacao     := OLD.situacao;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;

  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;

      -- GRAVA HISTÓRICO PARA TABELA FUNCIONARIO
      INSERT INTO historico.funcionario
      (matricula, idins, idset, idpes, situacao, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES
      (v_matricula, v_idins, v_idset, v_idpes, v_situacao, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');

   RETURN NEW;

END; $$;
