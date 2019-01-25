CREATE FUNCTION public.fcn_update_funcionario(numeric, integer, integer, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_matricula ALIAS for $1;
  v_id_ins ALIAS for $2;
  v_id_set ALIAS for $3;
  v_situacao ALIAS for $4;
      v_origem_gravacao ALIAS for $5;
      v_idpes_rev ALIAS for $6;
      v_idsis_rev ALIAS for $7;
  -- Outras variáveis
  v_id_set_aux integer;
  v_matricula_aux numeric;
  v_id_ins_aux integer;
  v_situacao_aux varchar(1);
BEGIN
  v_matricula_aux := v_matricula;
  v_id_ins_aux := v_id_ins;
  v_situacao_aux := v_situacao;
  IF v_id_set <= 0 THEN
    v_id_set_aux := NULL;
  ELSE
    v_id_set_aux := v_id_set;
  END IF;

  IF v_id_set_aux IS NULL AND v_id_set = -1 THEN
    -- Sql utilizado para ativar e desativar o registro na tabela funcionário
     UPDATE cadastro.funcionario
      SET situacao=v_situacao_aux,
      origem_gravacao = v_origem_gravacao,
      idpes_rev = v_idpes_rev,
      idsis_rev = v_idsis_rev,
      data_rev = CURRENT_DATE,
      operacao = 'A'
      WHERE
          matricula=v_matricula_aux AND
          idins=v_id_ins_aux;
  ELSE
    UPDATE cadastro.funcionario
      SET idset=v_id_set_aux,
          situacao=v_situacao_aux,
          origem_gravacao = v_origem_gravacao,
          idpes_rev = v_idpes_rev,
          idsis_rev = v_idsis_rev,
          data_rev = CURRENT_TIMESTAMP,
            operacao = 'A'
      WHERE
          matricula=v_matricula_aux AND
          idins=v_id_ins_aux;
  END IF;

  RETURN 0;
END;$_$;
