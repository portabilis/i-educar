CREATE FUNCTION consistenciacao.fcn_fone_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes       numeric;

  v_ddd_antigo      numeric;
  v_ddd_novo      numeric;
  v_fone_antigo     numeric;
  v_fone_novo     numeric;
  v_tipo_fone     numeric;

  v_comando     text;
  v_origem_gravacao   text;

  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;

  v_nova_credibilidade    numeric;

  v_registro      record;

  -- ID dos campos
  v_idcam_ddd_fone_residencial  numeric;
  v_idcam_fone_residencial  numeric;
  v_idcam_ddd_fone_comercial  numeric;
  v_idcam_fone_comercial    numeric;
  v_idcam_ddd_fone_celular  numeric;
  v_idcam_fone_celular    numeric;
  v_idcam_ddd_fax     numeric;
  v_idcam_fax     numeric;

  v_idcam_ddd     numeric;
  v_idcam_fone      numeric;

  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes   := NEW.idpes;
    v_tipo_fone := NEW.tipo;
    v_ddd_novo  := NEW.ddd;
    v_fone_novo := NEW.fone;

    IF TG_OP <> 'UPDATE' THEN
      v_ddd_antigo  := 0;
      v_fone_antigo := 0;
    ELSE
      v_ddd_antigo  := COALESCE(OLD.ddd, 0);
      v_fone_antigo := COALESCE(OLD.fone, 0);
    END IF;

    v_idcam_ddd_fone_residencial  := 39;
    v_idcam_fone_residencial  := 40;
    v_idcam_ddd_fone_comercial  := 41;
    v_idcam_fone_comercial    := 42;
    v_idcam_ddd_fone_celular  := 43;
    v_idcam_fone_celular    := 44;
    v_idcam_ddd_fax     := 45;
    v_idcam_fax     := 46;

    v_nova_credibilidade := 0;
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';

    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;

    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;

    IF v_tipo_fone = 1 THEN
      v_idcam_ddd := v_idcam_ddd_fone_residencial;
      v_idcam_fone := v_idcam_fone_residencial;
    ELSIF v_tipo_fone = 2 THEN
      v_idcam_ddd := v_idcam_ddd_fone_comercial;
      v_idcam_fone := v_idcam_fone_comercial;
    ELSIF v_tipo_fone = 3 THEN
      v_idcam_ddd := v_idcam_ddd_fone_celular;
      v_idcam_fone := v_idcam_fone_celular;
    ELSIF v_tipo_fone = 4 THEN
      v_idcam_ddd := v_idcam_ddd_fax;
      v_idcam_fone := v_idcam_fax;
    END IF;

    IF v_nova_credibilidade > 0 THEN
      IF v_ddd_novo <> v_ddd_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ddd||','||v_nova_credibilidade||');';
      END IF;

      IF v_fone_novo <> v_fone_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fone||','||v_nova_credibilidade||');';
      END IF;
    END IF;

    -- Verificar os campos Vazios ou Nulos
    IF v_ddd_novo <= 0 OR v_ddd_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ddd||','||v_sem_credibilidade||');';
    END IF;
    IF v_fone_novo <= 0 OR v_fone_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fone||','||v_sem_credibilidade||');';
    END IF;

  RETURN NEW;
END; $$;
