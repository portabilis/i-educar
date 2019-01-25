CREATE FUNCTION consistenciacao.fcn_endereco_pessoa_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes       numeric;
  v_cep_antigo      numeric;
  v_cep_novo      numeric;
  v_id_logradouro_antigo    numeric;
  v_id_logradouro_novo    numeric;
  v_id_bairro_antigo    numeric;
  v_id_bairro_novo    numeric;
  v_numero_antigo     numeric;
  v_numero_novo     numeric;
  v_letra_antiga      text;
  v_letra_nova      text;
  v_complemento_antigo    text;
  v_complemento_novo    text;
  v_tipo_endereco     numeric;
  v_comando     text;
  v_origem_gravacao   text;
  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;
  v_nova_credibilidade    numeric;

  v_registro      record;

  -- ID dos campos
  v_idcam_cep_correspondencia     numeric;
  v_idcam_id_logradouro_correspondencia   numeric;
  v_idcam_id_bairro_correspondencia   numeric;
  v_idcam_numero_correspondencia      numeric;
  v_idcam_letra_correspondencia     numeric;
  v_idcam_complemento_correspondencia   numeric;

  v_idcam_cep_residencial       numeric;
  v_idcam_id_logradouro_residencial   numeric;
  v_idcam_id_bairro_residencial     numeric;
  v_idcam_numero_residencial      numeric;
  v_idcam_letra_residencial     numeric;
  v_idcam_complemento_residencial     numeric;

  v_idcam_cep_comercial       numeric;
  v_idcam_id_logradouro_comercial     numeric;
  v_idcam_id_bairro_comercial     numeric;
  v_idcam_numero_comercial      numeric;
  v_idcam_letra_comercial       numeric;
  v_idcam_complemento_comercial     numeric;

  v_idcam_cep     numeric;
  v_idcam_id_logradouro   numeric;
  v_idcam_id_bairro   numeric;
  v_idcam_numero      numeric;
  v_idcam_letra     numeric;
  v_idcam_complemento   numeric;

  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes       := NEW.idpes;
    v_tipo_endereco     := NEW.tipo;

    v_cep_novo      := NEW.cep;
    v_id_logradouro_novo    := NEW.idlog;
    v_id_bairro_novo    := NEW.idbai;
    v_numero_novo     := NEW.numero;
    v_letra_nova      := NEW.letra;
    v_complemento_novo    := NEW.complemento;

    IF TG_OP <> 'UPDATE' THEN
      v_cep_antigo    := 0;
      v_id_logradouro_antigo  := 0;
      v_id_bairro_antigo  := 0;
      v_numero_antigo   := 0;
      v_letra_antiga    := '';
      v_complemento_antigo  := '';
    ELSE
      v_cep_antigo    := COALESCE(OLD.cep, 0);
      v_id_logradouro_antigo  := COALESCE(OLD.idlog, 0);
      v_id_bairro_antigo  := COALESCE(OLD.idbai, 0);
      v_numero_antigo   := COALESCE(OLD.numero, 0);
      v_letra_antiga    := COALESCE(OLD.letra, '');
      v_complemento_antigo  := COALESCE(OLD.complemento, '');
    END IF;

    v_idcam_cep_correspondencia     := 53;
    v_idcam_id_logradouro_correspondencia   := 47;
    v_idcam_id_bairro_correspondencia   := 52;
    v_idcam_numero_correspondencia      := 49;
    v_idcam_letra_correspondencia     := 50;
    v_idcam_complemento_correspondencia   := 51;
    v_idcam_cep_residencial       := 62;
    v_idcam_id_logradouro_residencial   := 56;
    v_idcam_id_bairro_residencial     := 61;
    v_idcam_numero_residencial      := 58;
    v_idcam_letra_residencial     := 59;
    v_idcam_complemento_residencial     := 60;
    v_idcam_cep_comercial       := 71;
    v_idcam_id_logradouro_comercial     := 65;
    v_idcam_id_bairro_comercial     := 70;
    v_idcam_numero_comercial      := 67;
    v_idcam_letra_comercial       := 68;
    v_idcam_complemento_comercial     := 69;

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
    IF v_tipo_endereco = 1 THEN
      v_idcam_cep     := v_idcam_cep_correspondencia;
      v_idcam_id_logradouro   := v_idcam_id_logradouro_correspondencia;
      v_idcam_id_bairro   := v_idcam_id_bairro_correspondencia;
      v_idcam_numero      := v_idcam_numero_correspondencia;
      v_idcam_letra     := v_idcam_letra_correspondencia;
      v_idcam_complemento   := v_idcam_complemento_correspondencia;
    ELSIF v_tipo_endereco = 2 THEN
      v_idcam_cep     := v_idcam_cep_residencial;
      v_idcam_id_logradouro   := v_idcam_id_logradouro_residencial;
      v_idcam_id_bairro   := v_idcam_id_bairro_residencial;
      v_idcam_numero      := v_idcam_numero_residencial;
      v_idcam_letra     := v_idcam_letra_residencial;
      v_idcam_complemento   := v_idcam_complemento_residencial;
    ELSIF v_tipo_endereco = 3 THEN
      v_idcam_cep     := v_idcam_cep_comercial;
      v_idcam_id_logradouro   := v_idcam_id_logradouro_comercial;
      v_idcam_id_bairro   := v_idcam_id_bairro_comercial;
      v_idcam_numero      := v_idcam_numero_comercial;
      v_idcam_letra     := v_idcam_letra_comercial;
      v_idcam_complemento   := v_idcam_complemento_comercial;
    END IF;
    IF v_nova_credibilidade > 0 THEN
      -- CEP
      IF v_cep_novo <> v_cep_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cep||','||v_nova_credibilidade||');';
      END IF;
      -- ID LOGRADOURO
      IF v_id_logradouro_novo <> v_id_logradouro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_logradouro||','||v_nova_credibilidade||');';
      END IF;
      -- BAIRRO
      IF v_id_bairro_novo <> v_id_bairro_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_bairro||','||v_nova_credibilidade||');';
      END IF;

      -- NUMERO
      IF v_numero_novo <> v_numero_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero||','||v_nova_credibilidade||');';
      END IF;

      -- LETRA
      IF v_letra_nova <> v_letra_antiga THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_letra||','||v_nova_credibilidade||');';
      END IF;

      -- COMPLEMENTO
      IF v_complemento_novo <> v_complemento_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_complemento||','||v_nova_credibilidade||');';
      END IF;
    END IF;

    -- Verificar os campos Vazios ou Nulos

    -- CEP
    IF v_cep_novo <= 0 OR v_cep_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cep||','||v_sem_credibilidade||');';
    END IF;

    -- ID LOGRADOURO
    IF v_id_logradouro_novo <= 0 OR v_id_logradouro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_logradouro||','||v_sem_credibilidade||');';
    END IF;

    -- BAIRRO
    IF v_id_bairro_novo <= 0 OR v_id_bairro_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_id_bairro||','||v_sem_credibilidade||');';
    END IF;

    -- NUMERO
    IF v_numero_novo <= 0 OR v_numero_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero||','||v_sem_credibilidade||');';
    END IF;

    -- LETRA
    IF TRIM(v_letra_nova)='' OR v_letra_nova IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_letra||','||v_sem_credibilidade||');';
    END IF;

    -- COMPLEMENTO
    IF TRIM(v_complemento_novo)='' OR v_complemento_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_complemento||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$;
