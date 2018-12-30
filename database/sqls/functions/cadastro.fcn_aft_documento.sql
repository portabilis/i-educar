CREATE FUNCTION cadastro.fcn_aft_documento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                DECLARE
                  v_idpes   numeric;
                  BEGIN
                    v_idpes := NEW.idpes;
                    EXECUTE E'DELETE FROM cadastro.documento WHERE ( (rg = \'0\' OR rg IS NULL) AND (idorg_exp_rg IS NULL) AND data_exp_rg IS NULL AND (sigla_uf_exp_rg IS NULL OR length(trim(sigla_uf_exp_rg))=0) AND (tipo_cert_civil = 0 OR tipo_cert_civil IS NULL) AND (num_termo = 0 OR num_termo IS NULL) AND (num_livro = \'0\' OR num_livro IS NULL) AND (num_livro = \'0\' OR num_livro IS NULL) AND (num_folha = 0 OR num_folha IS NULL) AND data_emissao_cert_civil IS NULL AND (sigla_uf_cert_civil IS NULL OR length(trim(sigla_uf_cert_civil))=0) AND (sigla_uf_cart_trabalho IS NULL OR length(trim(sigla_uf_cart_trabalho))=0) AND (cartorio_cert_civil IS NULL OR length(trim(cartorio_cert_civil))=0) AND (num_cart_trabalho = 0 OR num_cart_trabalho IS NULL) AND (serie_cart_trabalho = 0 OR serie_cart_trabalho IS NULL) AND data_emissao_cart_trabalho IS NULL AND (num_tit_eleitor = 0 OR num_tit_eleitor IS NULL) AND (zona_tit_eleitor = 0 OR zona_tit_eleitor IS NULL) AND (secao_tit_eleitor = 0 OR secao_tit_eleitor IS NULL) ) AND idpes='||quote_literal(v_idpes)||' AND certidao_nascimento is null';
                  RETURN NEW;
                END; $$;


CREATE FUNCTION cadastro.fcn_aft_documento_provisorio() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  DECLARE
    v_idpes       numeric;
    v_rg        text;
    v_uf_expedicao      text;
    v_verificacao_provisorio  numeric;

    v_comando     text;
    v_registro      record;

    BEGIN
      v_idpes     := NEW.idpes;
      v_rg      := COALESCE(NEW.rg, '-1');
      v_uf_expedicao    := TRIM(COALESCE(NEW.sigla_uf_exp_rg, ''));

      v_verificacao_provisorio:= 0;

      -- verificar se a situação do cadastro da pessoa é provisório
      FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
        IF v_registro.situacao = 'P' THEN
          v_verificacao_provisorio := 1;
        END IF;
      END LOOP;

      -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
      IF LENGTH(v_uf_expedicao) > 0 AND v_rg != '' AND v_rg != '-1' AND v_verificacao_provisorio = 1 THEN
        EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
      END IF;
    RETURN NEW;
  END; $$;
