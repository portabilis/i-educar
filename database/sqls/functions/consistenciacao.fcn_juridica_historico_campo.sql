CREATE FUNCTION consistenciacao.fcn_juridica_historico_campo() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_idpes       numeric;
  v_cnpj_novo     numeric;
  v_cnpj_antigo     numeric;
  v_fantasia_novo     text;
  v_fantasia_antigo   text;
  v_inscricao_estadual_novo numeric;
  v_inscricao_estadual_antigo numeric;

  v_comando     text;
  v_origem_gravacao   text;

  v_credibilidade_maxima    numeric;
  v_credibilidade_alta    numeric;
  v_sem_credibilidade   numeric;

  v_nova_credibilidade    numeric;

  v_registro      record;

  -- ID dos campos
  v_idcam_cnpj      numeric;
  v_idcam_fantasia    numeric;
  v_idcam_inscricao_estadual  numeric;

  /*
  consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
  cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
  */
  BEGIN
    v_idpes       := NEW.idpes;
    v_cnpj_novo     := NEW.cnpj;
    v_fantasia_novo     := NEW.fantasia;
    v_inscricao_estadual_novo := NEW.insc_estadual;

    IF TG_OP <> 'UPDATE' THEN
      v_cnpj_antigo     := 0;
      v_fantasia_antigo   := '';
      v_inscricao_estadual_antigo := 0;
    ELSE
      v_cnpj_antigo     := COALESCE(OLD.cnpj, 0);
      v_fantasia_antigo   := COALESCE(OLD.fantasia, '');
      v_inscricao_estadual_antigo := COALESCE(OLD.insc_estadual, 0);
    END IF;

    v_idcam_cnpj      := 1;
    v_idcam_fantasia    := 24;
    v_idcam_inscricao_estadual  := 25;

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

      -- CNPJ
      IF v_cnpj_novo <> v_cnpj_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cnpj||','||v_nova_credibilidade||');';
      END IF;

      -- NOME FANTASIA
      IF v_fantasia_novo <> v_fantasia_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fantasia||','||v_nova_credibilidade||');';
      END IF;

      -- INSCRIÇÃO ESTADUAL
      IF v_inscricao_estadual_novo <> v_inscricao_estadual_antigo THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_inscricao_estadual||','||v_nova_credibilidade||');';
      END IF;
    END IF;

    -- Verificar os campos Vazios ou Nulos
    -- CNPJ
    IF v_cnpj_novo <= 0 OR v_cnpj_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cnpj||','||v_sem_credibilidade||');';
    END IF;
    -- NOME FANTASIA
    IF TRIM(v_fantasia_novo)='' OR v_fantasia_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fantasia||','||v_sem_credibilidade||');';
    END IF;

    -- INSCRIÇÃO ESTADUAL
    IF v_inscricao_estadual_novo <= 0 OR v_inscricao_estadual_novo IS NULL THEN
      EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_inscricao_estadual||','||v_sem_credibilidade||');';
    END IF;
  RETURN NEW;
END; $$;
