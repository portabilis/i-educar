CREATE FUNCTION historico.fcn_delete_grava_historico_documento() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
   v_idpes    numeric;
   v_sigla_uf_exp_rg  char(2);
   v_rg     numeric;
   v_data_exp_rg  date;
   v_tipo_cert_civil  numeric;
   v_num_termo    numeric;
   v_num_livro    varchar;
   v_num_folha    numeric;
   v_data_emissao_cert_civil  date;
   v_sigla_uf_cert_civil  char(2);
   v_sigla_uf_cart_trabalho char(2);
   v_cartorio_cert_civil  varchar;
   v_num_cart_trabalho    numeric;
   v_serie_cart_trabalho  numeric;
   v_data_emissao_cart_trabalho date;
   v_num_tit_eleitor  numeric;
   v_zona_tit_eleitor numeric;
   v_secao_tit_eleitor  numeric;
   v_idorg_exp_rg   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
BEGIN
   v_idpes      := OLD.idpes;
   v_sigla_uf_exp_rg    := OLD.sigla_uf_exp_rg;
   v_rg       := OLD.rg;
   v_data_exp_rg    := OLD.data_exp_rg;
   v_tipo_cert_civil    := OLD.tipo_cert_civil;
   v_num_termo      := OLD.num_termo;
   v_num_livro      := OLD.num_livro;
   v_num_folha      := OLD.num_folha;
   v_data_emissao_cert_civil  := OLD.data_emissao_cert_civil;
   v_sigla_uf_cert_civil  := OLD.sigla_uf_cert_civil;
   v_sigla_uf_cart_trabalho := OLD.sigla_uf_cart_trabalho;
   v_cartorio_cert_civil  := OLD.cartorio_cert_civil;
   v_num_cart_trabalho    := OLD.num_cart_trabalho;
   v_serie_cart_trabalho  := OLD.serie_cart_trabalho;
   v_data_emissao_cart_trabalho := OLD.data_emissao_cart_trabalho;
   v_num_tit_eleitor    := OLD.num_tit_eleitor;
   v_zona_tit_eleitor   := OLD.zona_tit_eleitor;
   v_secao_tit_eleitor    := OLD.secao_tit_eleitor;
   v_idorg_exp_rg   := OLD.idorg_exp_rg;
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

      -- GRAVA HISTÓRICO PARA TABELA DOCUMENTO
      INSERT INTO historico.documento
      (idpes, sigla_uf_exp_rg, rg, data_exp_rg, tipo_cert_civil, num_termo, num_livro, num_folha, data_emissao_cert_civil, sigla_uf_cert_civil, sigla_uf_cart_trabalho, cartorio_cert_civil, num_cart_trabalho, serie_cart_trabalho, data_emissao_cart_trabalho, num_tit_eleitor, zona_tit_eleitor, secao_tit_eleitor, idorg_exp_rg, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES
      (v_idpes, v_sigla_uf_exp_rg, v_rg, v_data_exp_rg, v_tipo_cert_civil, v_num_termo, v_num_livro, v_num_folha, v_data_emissao_cert_civil, v_sigla_uf_cert_civil, v_sigla_uf_cart_trabalho, v_cartorio_cert_civil, v_num_cart_trabalho, v_serie_cart_trabalho, v_data_emissao_cart_trabalho, v_num_tit_eleitor, v_zona_tit_eleitor, v_secao_tit_eleitor, v_idorg_exp_rg, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, 'E');

   RETURN NEW;

END; $$;
