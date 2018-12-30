CREATE FUNCTION consistenciacao.fcn_pessoa_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes       numeric;
  v_nome_novo     text;
  v_nome_antigo     text;
  v_email_novo      text;
  v_email_antigo      text;
  v_url_novo      text;
  v_url_antigo      text;

  v_comando     text;
  v_origem_gravacao   text;

  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;

  v_nova_credibilidade    numeric;

  v_registro      record;

  -- ID dos campos
  v_idcam_nome      numeric;
  v_idcam_email     numeric;
  v_idcam_url     numeric;

  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes   := NEW.idpes;
    v_nome_novo := NEW.nome;
    v_email_novo  := NEW.email;
    v_url_novo  := NEW.url;

    IF TG_OP <> 'UPDATE' THEN
      v_nome_antigo := '';
      v_email_antigo  := '';
      v_url_antigo  := '';
    ELSE
      v_nome_antigo := COALESCE(OLD.nome, '');
      v_email_antigo  := COALESCE(OLD.email, '');
      v_url_antigo  := COALESCE(OLD.url, '');
    END IF;

    v_idcam_nome  := 2;
    v_idcam_email := 3;
    v_idcam_url := 4;

    v_nova_credibilidade := 0;
    v_credibilidade_maxima := 1;
    v_credibilidade_alta := 2;
    v_sem_credibilidade := 5;
    v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';

    FOR v_registro IN EXECUTE v_comando LOOP
      v_origem_gravacao := v_registro.origem_gravacao;
    END LOOP;

    IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
      v_nova_credibilidade := v_credibilidade_maxima;
    ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
      v_nova_credibilidade := v_credibilidade_alta;
    END IF;

    IF v_nova_credibilidade > 0 THEN

      -- NOME
      IF v_nome_novo <> v_nome_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome||','||v_nova_credibilidade||');';
      END IF;

      -- E-MAIL
      IF v_email_novo <> v_email_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_email||','||v_nova_credibilidade||');';
      END IF;

      -- URL
      IF v_url_novo <> v_url_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_url||','||v_nova_credibilidade||');';
      END IF;
    END IF;

    -- Verificar os campos Vazios ou Nulos
    -- NOME
    IF TRIM(v_nome_novo)='' OR v_nome_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome||','||v_sem_credibilidade||');';
    END IF;
    -- E-MAIL
    IF TRIM(v_email_novo)='' OR v_email_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_email||','||v_sem_credibilidade||');';
    END IF;
    -- URL
    IF TRIM(v_url_novo)='' OR v_url_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_url||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$;
