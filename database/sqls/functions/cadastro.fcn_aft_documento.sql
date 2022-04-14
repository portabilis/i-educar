CREATE OR REPLACE FUNCTION cadastro.fcn_aft_documento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                DECLARE
                  v_idpes   numeric;
                  BEGIN
                    v_idpes := NEW.idpes;
                    EXECUTE E'DELETE FROM cadastro.documento WHERE ( (rg = \'0\' OR rg IS NULL) AND (idorg_exp_rg IS NULL) AND data_exp_rg IS NULL AND (sigla_uf_exp_rg IS NULL OR length(trim(sigla_uf_exp_rg))=0) AND (tipo_cert_civil = 0 OR tipo_cert_civil IS NULL) AND (num_termo = 0 OR num_termo IS NULL) AND (num_livro = \'0\' OR num_livro IS NULL) AND (num_livro = \'0\' OR num_livro IS NULL) AND (num_folha = 0 OR num_folha IS NULL) AND data_emissao_cert_civil IS NULL AND (sigla_uf_cert_civil IS NULL OR length(trim(sigla_uf_cert_civil))=0) AND (sigla_uf_cart_trabalho IS NULL OR length(trim(sigla_uf_cart_trabalho))=0) AND (cartorio_cert_civil IS NULL OR length(trim(cartorio_cert_civil))=0) AND (num_cart_trabalho = 0 OR num_cart_trabalho IS NULL) AND (serie_cart_trabalho = 0 OR serie_cart_trabalho IS NULL) AND data_emissao_cart_trabalho IS NULL AND (num_tit_eleitor = 0 OR num_tit_eleitor IS NULL) AND (zona_tit_eleitor = 0 OR zona_tit_eleitor IS NULL) AND (secao_tit_eleitor = 0 OR secao_tit_eleitor IS NULL) ) AND idpes='||quote_literal(v_idpes)||' AND certidao_nascimento is null';
                  RETURN NEW;
                END; $$;
