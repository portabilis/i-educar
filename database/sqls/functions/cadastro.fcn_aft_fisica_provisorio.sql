CREATE FUNCTION cadastro.fcn_aft_fisica_provisorio() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes       numeric;
  v_idpes_mae     numeric;
  v_nome_mae      text;
  v_data_nascimento   text;
  v_verificacao_provisorio  numeric;

  v_comando     text;
  v_registro      record;

  BEGIN
    v_idpes     := NEW.idpes;
    v_idpes_mae   := COALESCE(NEW.idpes_mae, -1);
    v_nome_mae    := TRIM(COALESCE(NEW.nome_mae, ''));
    v_data_nascimento := COALESCE(TO_CHAR(NEW.data_nasc, 'DD/MM/YYYY'), '');

    v_verificacao_provisorio:= 0;

    -- verificar se a situação do cadastro da pessoa é provisório
    FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
      IF v_registro.situacao = 'P' THEN
        v_verificacao_provisorio := 1;
      END IF;
    END LOOP;

    -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
    IF v_data_nascimento <> '' AND (LENGTH(v_nome_mae) > 0 OR v_idpes_mae > 0) AND v_verificacao_provisorio = 1 THEN
      EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
    END IF;
  RETURN NEW;
END; $$;
