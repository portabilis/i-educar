CREATE FUNCTION cadastro.fcn_aft_fisica() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes       numeric;
  v_idpes_mae     numeric;
  v_idpes_pai     numeric;
  v_idpes_responsavel   numeric;
  v_idpes_conjuge     numeric;

  v_nome_mae      text;
  v_nome_pai      text;
  v_nome_responsavel    text;
  v_nome_conjuge      text;

  v_verificacao_mae   numeric;
  v_verificacao_pai   numeric;
  v_verificacao_conjuge   numeric;
  v_verificacao_responsavel numeric;

  v_num_aviso_mae     numeric;
  v_num_aviso_pai     numeric;
  v_num_aviso_conjuge   numeric;
  v_num_aviso_responsavel   numeric;

  v_existe_aviso_mae    numeric;
  v_existe_aviso_pai    numeric;
  v_existe_aviso_conjuge    numeric;
  v_existe_aviso_responsavel  numeric;

  v_comando     text;
  v_registro      record;

  BEGIN
    v_idpes     := NEW.idpes;
    v_idpes_mae   := NEW.idpes_mae;
    v_idpes_pai   := NEW.idpes_pai;
    v_idpes_responsavel := NEW.idpes_responsavel;
    v_idpes_conjuge   := NEW.idpes_con;
    v_nome_mae    := TRIM(NEW.nome_mae);
    v_nome_pai    := TRIM(NEW.nome_pai);
    v_nome_responsavel  := TRIM(NEW.nome_responsavel);
    v_nome_conjuge    := TRIM(NEW.nome_conjuge);

    v_num_aviso_mae   := 1;
    v_num_aviso_pai   := 2;
    v_num_aviso_conjuge := 3;
    v_num_aviso_responsavel := 4;

    v_verificacao_mae   := 0;
    v_verificacao_pai   := 0;
    v_verificacao_conjuge   := 0;
    v_verificacao_responsavel := 0;

    v_existe_aviso_mae    := 0;
    v_existe_aviso_pai    := 0;
    v_existe_aviso_conjuge    := 0;
    v_existe_aviso_responsavel  := 0;

    -- obter os avisos já existentes para a pessoa
    FOR v_registro IN SELECT aviso FROM cadastro.aviso_nome WHERE idpes=v_idpes LOOP
      IF v_registro.aviso = 1 THEN
        v_existe_aviso_mae := 1;
      ELSIF v_registro.aviso = 2 THEN
        v_existe_aviso_pai := 1;
      ELSIF v_registro.aviso = 3 THEN
        v_existe_aviso_conjuge := 1;
      ELSIF v_registro.aviso = 4 THEN
        v_existe_aviso_responsavel := 1;
      END IF;
    END LOOP;

    -- MAE
    IF v_idpes_mae > 0 AND v_idpes_mae IS NOT NULL AND LENGTH(v_nome_mae) > 0 AND v_nome_mae IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_mae, v_idpes_mae) LOOP
        v_verificacao_mae := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_mae := 1;
    END IF;

    -- PAI
    IF v_idpes_pai > 0 AND v_idpes_pai IS NOT NULL AND LENGTH(v_nome_pai) > 0 AND v_nome_pai IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_pai, v_idpes_pai) LOOP
        v_verificacao_pai := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_pai := 1;
    END IF;

    -- CONJUGE
    IF v_idpes_conjuge > 0 AND v_idpes_conjuge IS NOT NULL AND LENGTH(v_nome_conjuge) > 0 AND v_nome_conjuge IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_conjuge, v_idpes_conjuge) LOOP
        v_verificacao_conjuge := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_conjuge := 1;
    END IF;

    -- RESPONSAVEL
    IF v_idpes_responsavel > 0 AND v_idpes_responsavel IS NOT NULL AND LENGTH(v_nome_responsavel) > 0 AND v_nome_responsavel IS NOT NULL THEN
      FOR v_registro IN SELECT * from public.fcn_compara_nome_pessoa_fonetica(v_nome_responsavel, v_idpes_responsavel) LOOP
        v_verificacao_responsavel := v_registro.fcn_compara_nome_pessoa_fonetica;
      END LOOP;
    ELSE
      v_verificacao_responsavel := 1;
    END IF;
    -- Inserir ou Deletar aviso da MAE
    IF v_verificacao_mae = 0 AND v_existe_aviso_mae = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_mae||');';
    ELSIF v_verificacao_mae = 1 AND v_existe_aviso_mae = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_mae||';';
    END IF;

    -- Inserir ou Deletar aviso do PAI
    IF v_verificacao_pai = 0 AND v_existe_aviso_pai = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_pai||');';
    ELSIF v_verificacao_pai = 1 AND v_existe_aviso_pai = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_pai||';';
    END IF;

    -- Inserir ou Deletar aviso do CONJUGE
    IF v_verificacao_conjuge = 0 AND v_existe_aviso_conjuge = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_conjuge||');';
    ELSIF v_verificacao_conjuge = 1 AND v_existe_aviso_conjuge = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_conjuge||';';
    END IF;

    -- Inserir ou Deletar aviso do RESPONSAVEL
    IF v_verificacao_responsavel = 0 AND v_existe_aviso_responsavel = 0 THEN
      EXECUTE 'INSERT INTO cadastro.aviso_nome (idpes, aviso) VALUES ('||quote_literal(v_idpes)||','||v_num_aviso_responsavel||');';
    ELSIF v_verificacao_responsavel = 1 AND v_existe_aviso_responsavel = 1 THEN
      EXECUTE 'DELETE FROM cadastro.aviso_nome WHERE idpes='||quote_literal(v_idpes)||' AND aviso='||v_num_aviso_responsavel||';';
    END IF;
  RETURN NEW;
END; $$;
