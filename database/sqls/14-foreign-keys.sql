ALTER TABLE ONLY acesso.grupo_funcao
    ADD CONSTRAINT fk_funcao_grp_funcao FOREIGN KEY (idfunc, idsis, idmen) REFERENCES acesso.funcao(idfunc, idsis, idmen);

ALTER TABLE ONLY acesso.operacao_funcao
    ADD CONSTRAINT fk_funcao_operacao_funcao FOREIGN KEY (idfunc, idsis, idmen) REFERENCES acesso.funcao(idfunc, idsis, idmen);

ALTER TABLE ONLY acesso.grupo_operacao
    ADD CONSTRAINT fk_grp_fun_grp_operacao FOREIGN KEY (idmen, idsis, idgrp, idfunc) REFERENCES acesso.grupo_funcao(idmen, idsis, idgrp, idfunc);

ALTER TABLE ONLY acesso.grupo_funcao
    ADD CONSTRAINT fk_grp_menu_grp_funcao FOREIGN KEY (idgrp, idsis, idmen) REFERENCES acesso.grupo_menu(idgrp, idsis, idmen);

ALTER TABLE ONLY acesso.grupo_menu
    ADD CONSTRAINT fk_grp_sis_grp_menu FOREIGN KEY (idsis, idgrp) REFERENCES acesso.grupo_sistema(idsis, idgrp);

ALTER TABLE ONLY acesso.grupo_sistema
    ADD CONSTRAINT fk_grupo_grupo_sistema FOREIGN KEY (idgrp) REFERENCES acesso.grupo(idgrp);

ALTER TABLE ONLY acesso.usuario_grupo
    ADD CONSTRAINT fk_grupo_usuario_grupo FOREIGN KEY (idgrp) REFERENCES acesso.grupo(idgrp);

ALTER TABLE ONLY acesso.pessoa_instituicao
    ADD CONSTRAINT fk_inst_pessoa_instituicao FOREIGN KEY (idins) REFERENCES acesso.instituicao(idins);

ALTER TABLE ONLY acesso.funcao
    ADD CONSTRAINT fk_menu_funcao FOREIGN KEY (idmen, idsis) REFERENCES acesso.menu(idmen, idsis);

ALTER TABLE ONLY acesso.grupo_menu
    ADD CONSTRAINT fk_menu_grp_menu FOREIGN KEY (idmen, idsis) REFERENCES acesso.menu(idmen, idsis);

ALTER TABLE ONLY acesso.menu
    ADD CONSTRAINT fk_menu_menu FOREIGN KEY (menu_idsis, menu_idmen) REFERENCES acesso.menu(idsis, idmen);

ALTER TABLE ONLY acesso.grupo_operacao
    ADD CONSTRAINT fk_oper_func_grp_oper FOREIGN KEY (idmen, idsis, idfunc, idope) REFERENCES acesso.operacao_funcao(idmen, idsis, idfunc, idope);

ALTER TABLE ONLY acesso.operacao_funcao
    ADD CONSTRAINT fk_operacao_operacao_funcao FOREIGN KEY (idope) REFERENCES acesso.operacao(idope);

ALTER TABLE ONLY acesso.pessoa_instituicao
    ADD CONSTRAINT fk_pes_pessoa_instituicao FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY acesso.usuario
    ADD CONSTRAINT fk_pessoa_usuario FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY acesso.grupo_sistema
    ADD CONSTRAINT fk_sistema_grupo_sistema FOREIGN KEY (idsis) REFERENCES acesso.sistema(idsis);

ALTER TABLE ONLY acesso.menu
    ADD CONSTRAINT fk_sistema_menu FOREIGN KEY (idsis) REFERENCES acesso.sistema(idsis);

ALTER TABLE ONLY acesso.operacao
    ADD CONSTRAINT fk_sistema_operacao FOREIGN KEY (idsis) REFERENCES acesso.sistema(idsis);

ALTER TABLE ONLY acesso.usuario_grupo
    ADD CONSTRAINT fk_usuario_usuario_grupo FOREIGN KEY (login) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_alterar_usuario_cardapio FOREIGN KEY (login_alteracao) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT fk_baixa_guia_baixa_produto FOREIGN KEY (idbai) REFERENCES alimentos.baixa_guia_remessa(idbai);

ALTER TABLE ONLY alimentos.evento
    ADD CONSTRAINT fk_calendario_evento FOREIGN KEY (idcad) REFERENCES alimentos.calendario(idcad);

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_calendario_unidade FOREIGN KEY (idcad) REFERENCES alimentos.calendario(idcad);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_cancelar_usuario_guia_remessa FOREIGN KEY (login_cancelamento) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.cardapio_faixa_unidade
    ADD CONSTRAINT fk_cardapio_cardapio_faixa_unidade FOREIGN KEY (idcar) REFERENCES alimentos.cardapio(idcar);

ALTER TABLE ONLY alimentos.cardapio_produto
    ADD CONSTRAINT fk_cardapio_cardapio_produto FOREIGN KEY (idcar) REFERENCES alimentos.cardapio(idcar);

ALTER TABLE ONLY alimentos.cardapio_receita
    ADD CONSTRAINT fk_cardapio_cardapio_receita FOREIGN KEY (idcar) REFERENCES alimentos.cardapio(idcar);

ALTER TABLE ONLY alimentos.calendario
    ADD CONSTRAINT fk_cliente_calendario FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_cliente_cardapio FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT fk_cliente_contrato FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.composto_quimico
    ADD CONSTRAINT fk_cliente_cpquimico FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.fornecedor
    ADD CONSTRAINT fk_cliente_fornecedor FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.faixa_etaria
    ADD CONSTRAINT fk_cliente_grpatencao FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.grupo_quimico
    ADD CONSTRAINT fk_cliente_grpquimico FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_cliente_guia_remessa FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.log_guia_remessa
    ADD CONSTRAINT fk_cliente_log_guia FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.receita
    ADD CONSTRAINT fk_cliente_receita FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.tipo_produto
    ADD CONSTRAINT fk_cliente_tpproduto FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.tipo_refeicao
    ADD CONSTRAINT fk_cliente_tprefeicao FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.tipo_unidade
    ADD CONSTRAINT fk_cliente_tpunidade FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_cliente_unidade FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.unidade_produto
    ADD CONSTRAINT fk_cliente_uniproduto FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.contrato_produto
    ADD CONSTRAINT fk_contrato_contrato_produto FOREIGN KEY (idcon) REFERENCES alimentos.contrato(idcon);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_contrato_guia_remessa FOREIGN KEY (idcon) REFERENCES alimentos.contrato(idcon);

ALTER TABLE ONLY alimentos.faixa_composto_quimico
    ADD CONSTRAINT fk_cp_quimico_faixa_cp_quimico FOREIGN KEY (idcom) REFERENCES alimentos.composto_quimico(idcom);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_emitir_usuario_guia_remessa FOREIGN KEY (login_emissao) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.unidade_faixa_etaria
    ADD CONSTRAINT fk_faixa_etaria_unidade_faixa FOREIGN KEY (idfae) REFERENCES alimentos.faixa_etaria(idfae);

ALTER TABLE ONLY alimentos.faixa_composto_quimico
    ADD CONSTRAINT fk_faixa_faixa_cp_quimico FOREIGN KEY (idfae) REFERENCES alimentos.faixa_etaria(idfae);

ALTER TABLE ONLY alimentos.cardapio_faixa_unidade
    ADD CONSTRAINT fk_faixa_uni_cardapio_faixa_unidade FOREIGN KEY (idfeu) REFERENCES alimentos.unidade_faixa_etaria(idfeu);

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT fk_fornecedor_contrato FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_fornecedor_guia_remessa FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);

ALTER TABLE ONLY alimentos.produto_fornecedor
    ADD CONSTRAINT fk_fornecedor_produto_fornecedor FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);

ALTER TABLE ONLY alimentos.fornecedor_unidade_atendida
    ADD CONSTRAINT fk_fornecedor_unidade FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);

ALTER TABLE ONLY alimentos.composto_quimico
    ADD CONSTRAINT fk_grupo_cp_quimico_cp_quimico FOREIGN KEY (idgrpq) REFERENCES alimentos.grupo_quimico(idgrpq);

ALTER TABLE ONLY alimentos.guia_remessa_produto
    ADD CONSTRAINT fk_guia_guia_remessa_produto FOREIGN KEY (idgui) REFERENCES alimentos.guia_remessa(idgui);

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT fk_guia_produto_baixa_produto FOREIGN KEY (idgup) REFERENCES alimentos.guia_remessa_produto(idgup);

ALTER TABLE ONLY alimentos.baixa_guia_remessa
    ADD CONSTRAINT fk_guia_remessa_baixa_guia FOREIGN KEY (idgui) REFERENCES alimentos.guia_remessa(idgui);

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT fk_guia_remessa_guia_pro_diario FOREIGN KEY (idgui) REFERENCES alimentos.guia_remessa(idgui);

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_incluir_usuario_cardapio FOREIGN KEY (login_inclusao) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.medidas_caseiras
    ADD CONSTRAINT fk_medidas_caseiras_cliente FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.cliente
    ADD CONSTRAINT fk_pessoa_cliente FOREIGN KEY (idpes) REFERENCES alimentos.pessoa(idpes);

ALTER TABLE ONLY alimentos.fornecedor
    ADD CONSTRAINT fk_pessoa_fornecedor FOREIGN KEY (idpes) REFERENCES alimentos.pessoa(idpes);

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_pessoa_unidade_atend FOREIGN KEY (idpes) REFERENCES alimentos.pessoa(idpes);

ALTER TABLE ONLY alimentos.produto_composto_quimico
    ADD CONSTRAINT fk_prod_cp_quimico_cp_quimico FOREIGN KEY (idcom) REFERENCES alimentos.composto_quimico(idcom);

ALTER TABLE ONLY alimentos.produto_composto_quimico
    ADD CONSTRAINT fk_prod_cp_quimico_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.cardapio_produto
    ADD CONSTRAINT fk_produto_cardapio_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_cliente FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.contrato_produto
    ADD CONSTRAINT fk_produto_contrato_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_fornecedor FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT fk_produto_guia_pro_diario FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.guia_remessa_produto
    ADD CONSTRAINT fk_produto_guia_remessa_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_cliente FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_medidas FOREIGN KEY (idmedcas, idcli) REFERENCES alimentos.medidas_caseiras(idmedcas, idcli);

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.produto_fornecedor
    ADD CONSTRAINT fk_produto_produto_fornecedor FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_tipo FOREIGN KEY (idtip) REFERENCES alimentos.tipo_produto(idtip);

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_unidade FOREIGN KEY (idunp, idcli) REFERENCES alimentos.unidade_produto(idunp, idcli);

ALTER TABLE ONLY alimentos.receita_composto_quimico
    ADD CONSTRAINT fk_rec_cp_quimico_cp_quimico FOREIGN KEY (idcom) REFERENCES alimentos.composto_quimico(idcom);

ALTER TABLE ONLY alimentos.receita_composto_quimico
    ADD CONSTRAINT fk_rec_cp_quimico_receita FOREIGN KEY (idrec) REFERENCES alimentos.receita(idrec);

ALTER TABLE ONLY alimentos.receita_produto
    ADD CONSTRAINT fk_rec_prod_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);

ALTER TABLE ONLY alimentos.receita_produto
    ADD CONSTRAINT fk_rec_prod_receita FOREIGN KEY (idrec) REFERENCES alimentos.receita(idrec);

ALTER TABLE ONLY alimentos.cardapio_receita
    ADD CONSTRAINT fk_receita_cardapio_receita FOREIGN KEY (idrec) REFERENCES alimentos.receita(idrec);

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_tipo_uni_uni_atendida FOREIGN KEY (idtip) REFERENCES alimentos.tipo_unidade(idtip);

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_tp_refeicao_cardapio FOREIGN KEY (idtre) REFERENCES alimentos.tipo_refeicao(idtre);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_uni_atend_guia_remessa FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);

ALTER TABLE ONLY alimentos.unidade_faixa_etaria
    ADD CONSTRAINT fk_uni_atend_uni_faixa_eta FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT fk_unidade_atendida_guia_pro_diario FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);

ALTER TABLE ONLY alimentos.fornecedor_unidade_atendida
    ADD CONSTRAINT fk_unidade_fornecedor FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT fk_usuario_baixa_guia FOREIGN KEY (login_baixa) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.baixa_guia_remessa
    ADD CONSTRAINT fk_usuario_baixa_guia_remessa FOREIGN KEY (login_baixa) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT fk_usuario_contrato FOREIGN KEY (login) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY alimentos.log_guia_remessa
    ADD CONSTRAINT fk_usuario_log_guia FOREIGN KEY (login) REFERENCES acesso.usuario(login);

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT cartorio_cert_civil_inep_fk FOREIGN KEY (cartorio_cert_civil_inep) REFERENCES cadastro.codigo_cartorio_inep(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica_foto
    ADD CONSTRAINT fisica_foto_idpes_fkey FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fisica_ref_cod_religiao FOREIGN KEY (ref_cod_religiao) REFERENCES pmieducar.religiao(cod_religiao);

ALTER TABLE ONLY cadastro.fisica_sangue
    ADD CONSTRAINT fisica_sangue_idpes_fkey FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.aviso_nome
    ADD CONSTRAINT fk_aviso_nome_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes) ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes);

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_orgao_emissor_rg FOREIGN KEY (idorg_exp_rg) REFERENCES cadastro.orgao_emissor_rg(idorg_rg) ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_uf_cart_trabalho FOREIGN KEY (sigla_uf_cart_trabalho) REFERENCES public.uf(sigla_uf);

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_uf_cert_civil FOREIGN KEY (sigla_uf_cert_civil) REFERENCES public.uf(sigla_uf);

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_uf_rg FOREIGN KEY (sigla_uf_exp_rg) REFERENCES public.uf(sigla_uf);

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_tipo_log FOREIGN KEY (idtlog) REFERENCES urbano.tipo_logradouro(idtlog);

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_uf FOREIGN KEY (sigla_uf) REFERENCES public.uf(sigla_uf);

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pes_cep_log_bai FOREIGN KEY (cep, idbai, idlog) REFERENCES urbano.cep_logradouro_bairro(cep, idbai, idlog) ON UPDATE CASCADE;

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes) ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_escolaridade FOREIGN KEY (idesco) REFERENCES cadastro.escolaridade(idesco);

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_estado_civil FOREIGN KEY (ideciv) REFERENCES cadastro.estado_civil(ideciv);

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_municipio FOREIGN KEY (idmun_nascimento) REFERENCES public.municipio(idmun);

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_ocupacao FOREIGN KEY (idocup) REFERENCES cadastro.ocupacao(idocup);

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pais FOREIGN KEY (idpais_estrangeiro) REFERENCES public.pais(idpais);

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_conjuge FOREIGN KEY (idpes_con) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_mae FOREIGN KEY (idpes_mae) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_pai FOREIGN KEY (idpes_pai) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_responsavel FOREIGN KEY (idpes_responsavel) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes);

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_instituicao FOREIGN KEY (idins) REFERENCES acesso.instituicao(idins);

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_setor FOREIGN KEY (idset) REFERENCES public.setor(idset);

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.historico_cartao
    ADD CONSTRAINT fk_hist_cartao_pes_cidadao FOREIGN KEY (idpes_cidadao) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.historico_cartao
    ADD CONSTRAINT fk_hist_cartao_pes_emitiu FOREIGN KEY (idpes_emitiu) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_juridica_socio FOREIGN KEY (idpes_juridica) REFERENCES cadastro.juridica(idpes);

ALTER TABLE ONLY cadastro.pessoa_fonetico
    ADD CONSTRAINT fk_pessoa_fonetico_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_pessoa_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_pessoa_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_pessoa_socio FOREIGN KEY (idpes_fisica) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY cadastro.codigo_cartorio_inep
    ADD CONSTRAINT fk_ref_sigla_uf FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf);

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY cadastro.fisica_deficiencia
    ADD CONSTRAINT pessoa_deficiencia_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_deficiencia) REFERENCES cadastro.deficiencia(cod_deficiencia) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica_deficiencia
    ADD CONSTRAINT pessoa_deficiencia_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica_raca
    ADD CONSTRAINT pessoa_raca_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_raca) REFERENCES cadastro.raca(cod_raca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.fisica_raca
    ADD CONSTRAINT pessoa_raca_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.religiao
    ADD CONSTRAINT religiao_idpes_cad_fkey FOREIGN KEY (idpes_cad) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY cadastro.religiao
    ADD CONSTRAINT religiao_idpes_exc_fkey FOREIGN KEY (idpes_exc) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT fk_campo_metadado_campo_consis FOREIGN KEY (idcam) REFERENCES consistenciacao.campo_consistenciacao(idcam);

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT fk_campo_metadado_metadado FOREIGN KEY (idmet) REFERENCES consistenciacao.metadado(idmet);

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT fk_campo_metadado_regra_campo FOREIGN KEY (idreg) REFERENCES consistenciacao.regra_campo(idreg);

ALTER TABLE ONLY consistenciacao.confrontacao
    ADD CONSTRAINT fk_confrontacao_metadado FOREIGN KEY (idmet) REFERENCES consistenciacao.metadado(idmet);

ALTER TABLE ONLY consistenciacao.confrontacao
    ADD CONSTRAINT fk_confrontacao_pessoa_instituicao FOREIGN KEY (idins, idpes) REFERENCES acesso.pessoa_instituicao(idins, idpes);

ALTER TABLE ONLY consistenciacao.historico_campo
    ADD CONSTRAINT fk_hist_campo_campo_consist FOREIGN KEY (idcam) REFERENCES consistenciacao.campo_consistenciacao(idcam) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.historico_campo
    ADD CONSTRAINT fk_historico_campo_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
    ADD CONSTRAINT fk_inc_pessoa_possivel_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
    ADD CONSTRAINT fk_inc_pessoa_possivel_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
    ADD CONSTRAINT fk_inc_tipo_inc_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
    ADD CONSTRAINT fk_inc_tipo_inc_tipo_incoerencia FOREIGN KEY (id_tipo_inc) REFERENCES consistenciacao.tipo_incoerencia(id_tipo_inc);

ALTER TABLE ONLY consistenciacao.incoerencia
    ADD CONSTRAINT fk_incoerencia_confrontacao FOREIGN KEY (idcon) REFERENCES consistenciacao.confrontacao(idcon);

ALTER TABLE ONLY consistenciacao.incoerencia_documento
    ADD CONSTRAINT fk_incoerencia_documento_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.incoerencia_endereco
    ADD CONSTRAINT fk_incoerencia_endereco_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.incoerencia_fone
    ADD CONSTRAINT fk_incoerencia_fone_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;

ALTER TABLE ONLY consistenciacao.metadado
    ADD CONSTRAINT fk_metadado_fonte FOREIGN KEY (idfon) REFERENCES consistenciacao.fonte(idfon);

ALTER TABLE ONLY consistenciacao.ocorrencia_regra_campo
    ADD CONSTRAINT fk_oco_reg_cam_regra_campo FOREIGN KEY (idreg) REFERENCES consistenciacao.regra_campo(idreg);

ALTER TABLE ONLY consistenciacao.tipo_incoerencia
    ADD CONSTRAINT fk_tipo_incoerencia_campo_consis FOREIGN KEY (idcam) REFERENCES consistenciacao.campo_consistenciacao(idcam);

ALTER TABLE ONLY modules.calendario_turma
    ADD CONSTRAINT calendario_turma_calendario_dia_fk FOREIGN KEY (calendario_ano_letivo_id, mes, dia) REFERENCES pmieducar.calendario_dia(ref_cod_calendario_ano_letivo, mes, dia) MATCH FULL ON DELETE CASCADE;

ALTER TABLE ONLY modules.componente_curricular_ano_escolar
    ADD CONSTRAINT componente_curricular_ano_escolar_fk FOREIGN KEY (componente_curricular_id) REFERENCES modules.componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.componente_curricular
    ADD CONSTRAINT componente_curricular_area_conhecimento_fk FOREIGN KEY (area_conhecimento_id, instituicao_id) REFERENCES modules.area_conhecimento(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.componente_curricular_turma
    ADD CONSTRAINT componente_curricular_turma_componente_curricular_fkey FOREIGN KEY (componente_curricular_id) REFERENCES modules.componente_curricular(id) ON DELETE RESTRICT;

ALTER TABLE ONLY modules.componente_curricular_turma
    ADD CONSTRAINT componente_curricular_turma_fkey FOREIGN KEY (turma_id) REFERENCES pmieducar.turma(cod_turma) ON DELETE CASCADE;

ALTER TABLE ONLY modules.docente_licenciatura
    ADD CONSTRAINT docente_licenciatura_ies_fk FOREIGN KEY (ies_id) REFERENCES modules.educacenso_ies(id) ON DELETE RESTRICT;

ALTER TABLE ONLY modules.educacenso_cod_aluno
    ADD CONSTRAINT educacenso_cod_aluno_cod_aluno_fk FOREIGN KEY (cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON DELETE CASCADE;

ALTER TABLE ONLY modules.educacenso_cod_escola
    ADD CONSTRAINT educacenso_cod_escola_cod_escola_fk FOREIGN KEY (cod_escola) REFERENCES pmieducar.escola(cod_escola) ON DELETE CASCADE;

ALTER TABLE ONLY modules.educacenso_cod_turma
    ADD CONSTRAINT educacenso_cod_turma_cod_turma_fk FOREIGN KEY (cod_turma) REFERENCES pmieducar.turma(cod_turma) ON DELETE CASCADE;

ALTER TABLE ONLY modules.empresa_transporte_escolar
    ADD CONSTRAINT empresa_transporte_escolar_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes);

ALTER TABLE ONLY modules.empresa_transporte_escolar
    ADD CONSTRAINT empresa_transporte_escolar_ref_resp_idpes_fkey FOREIGN KEY (ref_resp_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.etapas_curso_educacenso
    ADD CONSTRAINT etapas_curso_educacenso_curso_fk FOREIGN KEY (curso_id) REFERENCES pmieducar.curso(cod_curso);

ALTER TABLE ONLY modules.etapas_curso_educacenso
    ADD CONSTRAINT etapas_curso_educacenso_etapa_fk FOREIGN KEY (etapa_id) REFERENCES modules.etapas_educacenso(id);

ALTER TABLE ONLY modules.falta_componente_curricular
    ADD CONSTRAINT falta_componente_curricular_falta_aluno_fk FOREIGN KEY (falta_aluno_id) REFERENCES modules.falta_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.falta_geral
    ADD CONSTRAINT falta_geral_falta_aluno_fk FOREIGN KEY (falta_aluno_id) REFERENCES modules.falta_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.ficha_medica_aluno
    ADD CONSTRAINT ficha_medica_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.ponto_transporte_escolar
    ADD CONSTRAINT fk_ponto_cep_log_bai FOREIGN KEY (idbai, idlog, cep) REFERENCES urbano.cep_logradouro_bairro(idbai, idlog, cep);

ALTER TABLE ONLY modules.itinerario_transporte_escolar
    ADD CONSTRAINT itinerario_transporte_escolar_ref_cod_rota_transporte_escolar_f FOREIGN KEY (ref_cod_rota_transporte_escolar) REFERENCES modules.rota_transporte_escolar(cod_rota_transporte_escolar);

ALTER TABLE ONLY modules.media_geral
    ADD CONSTRAINT media_geral_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.moradia_aluno
    ADD CONSTRAINT moradia_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.nota_exame
    ADD CONSTRAINT moradia_aluno_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.motorista
    ADD CONSTRAINT motorista_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar) REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.motorista
    ADD CONSTRAINT motorista_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.nota_componente_curricular_media
    ADD CONSTRAINT nota_componente_curricular_media_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.nota_componente_curricular
    ADD CONSTRAINT nota_componente_curricular_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.nota_geral
    ADD CONSTRAINT nota_nota_geral_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.parecer_componente_curricular
    ADD CONSTRAINT parecer_componente_curricular_parecer_aluno_fk FOREIGN KEY (parecer_aluno_id) REFERENCES modules.parecer_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.parecer_geral
    ADD CONSTRAINT parecer_geral_parecer_aluno_fk FOREIGN KEY (parecer_aluno_id) REFERENCES modules.parecer_aluno(id) ON DELETE CASCADE;

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_cod_ponto_transporte_escolar_fkey FOREIGN KEY (ref_cod_ponto_transporte_escolar) REFERENCES modules.ponto_transporte_escolar(cod_ponto_transporte_escolar);

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_cod_rota_transporte_escolar_fkey FOREIGN KEY (ref_cod_rota_transporte_escolar) REFERENCES modules.rota_transporte_escolar(cod_rota_transporte_escolar);

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_idpes_destino_fkey FOREIGN KEY (ref_idpes_destino) REFERENCES cadastro.juridica(idpes);

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.itinerario_transporte_escolar
    ADD CONSTRAINT ponto_transporte_escolar_ref_cod_veiculo_fkey FOREIGN KEY (ref_cod_veiculo) REFERENCES modules.veiculo(cod_veiculo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.professor_turma_disciplina
    ADD CONSTRAINT professor_turma_disciplina_componente_curricular_id_fk FOREIGN KEY (componente_curricular_id) REFERENCES modules.componente_curricular(id) MATCH FULL ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.professor_turma_disciplina
    ADD CONSTRAINT professor_turma_disciplina_professor_turma_id_fk FOREIGN KEY (professor_turma_id) REFERENCES modules.professor_turma(id) MATCH FULL ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_servidor_id_fk FOREIGN KEY (servidor_id, instituicao_id) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) MATCH FULL ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_turma_id_fk FOREIGN KEY (turma_id) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_turma_turno_id_fk FOREIGN KEY (turno_id) REFERENCES pmieducar.turma_turno(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.config_movimento_geral
    ADD CONSTRAINT ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie);

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_formula_media_formula_media_fk FOREIGN KEY (formula_media_id, instituicao_id) REFERENCES modules.formula_media(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_formula_media_formula_recuperacao_fk FOREIGN KEY (formula_recuperacao_id, instituicao_id) REFERENCES modules.formula_media(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao_recuperacao
    ADD CONSTRAINT regra_avaliacao_regra_avaliacao_recuperacao_fk FOREIGN KEY (regra_avaliacao_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_fk_regra_avaliacao_diferenciada_id FOREIGN KEY (regra_avaliacao_diferenciada_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_fk_regra_avaliacao_id FOREIGN KEY (regra_avaliacao_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_fk_serie_id FOREIGN KEY (serie_id) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_tabela_arredondamento_fk FOREIGN KEY (tabela_arredondamento_id, instituicao_id) REFERENCES modules.tabela_arredondamento(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_diferenciada_fk FOREIGN KEY (regra_diferenciada_id) REFERENCES modules.regra_avaliacao(id);

ALTER TABLE ONLY modules.rota_transporte_escolar
    ADD CONSTRAINT rota_transporte_escolar_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar) REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.rota_transporte_escolar
    ADD CONSTRAINT rota_transporte_escolar_ref_idpes_destino_fkey FOREIGN KEY (ref_idpes_destino) REFERENCES cadastro.juridica(idpes);

ALTER TABLE ONLY modules.tabela_arredondamento_valor
    ADD CONSTRAINT tabela_arredondamento_tabela_arredondamento_valor_fk FOREIGN KEY (tabela_arredondamento_id) REFERENCES modules.tabela_arredondamento(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.transporte_aluno
    ADD CONSTRAINT transporte_aluno_aluno_fk FOREIGN KEY (aluno_id) REFERENCES pmieducar.aluno(cod_aluno) ON DELETE CASCADE;

ALTER TABLE ONLY modules.uniforme_aluno
    ADD CONSTRAINT uniforme_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY modules.veiculo
    ADD CONSTRAINT veiculo_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar) REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar);

ALTER TABLE ONLY modules.veiculo
    ADD CONSTRAINT veiculo_ref_cod_tipo_veiculo_fkey FOREIGN KEY (ref_cod_tipo_veiculo) REFERENCES modules.tipo_veiculo(cod_tipo_veiculo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_ref_cod_categoria_fkey FOREIGN KEY (ref_cod_categoria) REFERENCES pmiacoes.categoria(cod_categoria) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_cod_foto_portal_fkey FOREIGN KEY (ref_cod_foto_portal) REFERENCES portal.foto_portal(cod_foto_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_cod_not_portal_fkey FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo
    ADD CONSTRAINT acao_governo_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo
    ADD CONSTRAINT acao_governo_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_cod_setor_fkey FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.categoria
    ADD CONSTRAINT categoria_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.categoria
    ADD CONSTRAINT categoria_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_ref_cod_funcionario_cad_fkey FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiacoes.secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_ref_cod_setor_fkey FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT fk_to_imagem_ico FOREIGN KEY (ref_cod_ico) REFERENCES portal.imagem(cod_imagem) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT fk_to_tutor FOREIGN KEY (ref_cod_tutormenu) REFERENCES pmicontrolesis.tutormenu(cod_tutormenu);

ALTER TABLE ONLY pmicontrolesis.foto_evento
    ADD CONSTRAINT foto_evento_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmicontrolesis.foto_vinc
    ADD CONSTRAINT foto_vinc_ref_cod_acontecimento_fkey FOREIGN KEY (ref_cod_acontecimento) REFERENCES pmicontrolesis.acontecimento(cod_acontecimento);

ALTER TABLE ONLY pmicontrolesis.foto_vinc
    ADD CONSTRAINT foto_vinc_ref_cod_foto_evento_fkey FOREIGN KEY (ref_cod_foto_evento) REFERENCES pmicontrolesis.foto_evento(cod_foto_evento);

ALTER TABLE ONLY pmicontrolesis.itinerario
    ADD CONSTRAINT itinerario_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.itinerario
    ADD CONSTRAINT itinerario_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.menu_portal
    ADD CONSTRAINT menu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.menu_portal
    ADD CONSTRAINT menu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT menu_ref_cod_menu_pai_fkey FOREIGN KEY (ref_cod_menu_pai) REFERENCES pmicontrolesis.menu(cod_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT menu_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmicontrolesis.portais
    ADD CONSTRAINT portais_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.portais
    ADD CONSTRAINT portais_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.servicos
    ADD CONSTRAINT servicos_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.servicos
    ADD CONSTRAINT servicos_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.sistema
    ADD CONSTRAINT sistema_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.sistema
    ADD CONSTRAINT sistema_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_ref_cod_menu_portal_fk FOREIGN KEY (ref_cod_menu_portal) REFERENCES pmicontrolesis.menu_portal(cod_menu_portal);

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.telefones
    ADD CONSTRAINT telefones_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.telefones
    ADD CONSTRAINT telefones_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT tipo_acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_tipo_acontecimento) REFERENCES pmicontrolesis.tipo_acontecimento(cod_tipo_acontecimento);

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT topo_portal_ref_cod_menu_portal_fk FOREIGN KEY (ref_cod_menu_portal) REFERENCES pmicontrolesis.menu_portal(cod_menu_portal);

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_cod_diaria_grupo_fkey FOREIGN KEY (ref_cod_diaria_grupo) REFERENCES pmidrh.diaria_grupo(cod_diaria_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_cod_setor FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor);

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_funcionario_cadastro_fkey FOREIGN KEY (ref_funcionario_cadastro) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_funcionario_fkey FOREIGN KEY (ref_funcionario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.diaria_valores
    ADD CONSTRAINT diaria_valores_ref_cod_diaria_grupo_fkey FOREIGN KEY (ref_cod_diaria_grupo) REFERENCES pmidrh.diaria_grupo(cod_diaria_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.diaria_valores
    ADD CONSTRAINT diaria_valores_ref_funcionario_cadastro_fkey FOREIGN KEY (ref_funcionario_cadastro) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT fk_setor_pai FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT fk_to_idpes_resp FOREIGN KEY (ref_idpes_resp) REFERENCES cadastro.fisica(idpes);

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT setor_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT setor_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_ref_cod_acervo_assunto_fkey FOREIGN KEY (ref_cod_acervo_assunto) REFERENCES pmieducar.acervo_assunto(cod_acervo_assunto) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_ref_cod_acervo_autor_fkey FOREIGN KEY (ref_cod_acervo_autor) REFERENCES pmieducar.acervo_autor(cod_acervo_autor) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_autor_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_autor_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_idtlog_fkey FOREIGN KEY (ref_idtlog) REFERENCES urbano.tipo_logradouro(idtlog) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_sigla_uf_fkey FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_colecao_fkey FOREIGN KEY (ref_cod_acervo_colecao) REFERENCES pmieducar.acervo_colecao(cod_acervo_colecao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_editora_fkey FOREIGN KEY (ref_cod_acervo_editora) REFERENCES pmieducar.acervo_editora(cod_acervo_editora) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_idioma_fkey FOREIGN KEY (ref_cod_acervo_idioma) REFERENCES pmieducar.acervo_idioma(cod_acervo_idioma) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_exemplar_tipo_fkey FOREIGN KEY (ref_cod_exemplar_tipo) REFERENCES pmieducar.exemplar_tipo(cod_exemplar_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
    ADD CONSTRAINT aluno_aluno_beneficio_aluno_beneficio_fk FOREIGN KEY (aluno_beneficio_id) REFERENCES pmieducar.aluno_beneficio(cod_aluno_beneficio);

ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
    ADD CONSTRAINT aluno_aluno_beneficio_aluno_fk FOREIGN KEY (aluno_id) REFERENCES pmieducar.aluno(cod_aluno);

ALTER TABLE ONLY pmieducar.aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.aluno_historico_altura_peso
    ADD CONSTRAINT aluno_historico_altura_peso_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno);

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_ref_cod_modulo_fkey FOREIGN KEY (ref_cod_modulo) REFERENCES pmieducar.modulo(cod_modulo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ano) REFERENCES pmieducar.escola_ano_letivo(ref_cod_escola, ano) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa
    ADD CONSTRAINT auditoria_falta_componente_di_ref_cod_componente_curricula_fkey FOREIGN KEY (ref_cod_componente_curricular) REFERENCES modules.componente_curricular(id);

ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa
    ADD CONSTRAINT auditoria_falta_componente_dispensa_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula);

ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
    ADD CONSTRAINT auditoria_nota_dispensa_ref_cod_componente_curricular_fkey FOREIGN KEY (ref_cod_componente_curricular) REFERENCES modules.componente_curricular(id);

ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
    ADD CONSTRAINT auditoria_nota_dispensa_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula);

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.biblioteca_dia
    ADD CONSTRAINT biblioteca_dia_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.biblioteca_feriados
    ADD CONSTRAINT biblioteca_feriados_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.biblioteca
    ADD CONSTRAINT biblioteca_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.biblioteca
    ADD CONSTRAINT biblioteca_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.biblioteca_usuario
    ADD CONSTRAINT biblioteca_usuario_ref_cod_biblioteca_fk FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.bloqueio_lancamento_faltas_notas
    ADD CONSTRAINT bloqueio_lancamento_faltas_notas_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_ref_cod_calendario_anotacao_fkey FOREIGN KEY (ref_cod_calendario_anotacao) REFERENCES pmieducar.calendario_anotacao(cod_calendario_anotacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_ref_cod_calendario_ano_letivo, ref_mes, ref_dia) REFERENCES pmieducar.calendario_dia(ref_cod_calendario_ano_letivo, mes, dia) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_cod_calendario_ano_letivo) REFERENCES pmieducar.calendario_ano_letivo(cod_calendario_ano_letivo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_cod_calendario_dia_motivo_fkey FOREIGN KEY (ref_cod_calendario_dia_motivo) REFERENCES pmieducar.calendario_dia_motivo(cod_calendario_dia_motivo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT candidato_reserva_vaga_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);

ALTER TABLE ONLY pmieducar.categoria_nivel
    ADD CONSTRAINT categoria_nivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.categoria_nivel
    ADD CONSTRAINT categoria_nivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_cod_motivo_suspensao_fkey FOREIGN KEY (ref_cod_motivo_suspensao) REFERENCES pmieducar.motivo_suspensao(cod_motivo_suspensao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_usuario_libera_fkey FOREIGN KEY (ref_usuario_libera) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_usuario_suspende_fkey FOREIGN KEY (ref_usuario_suspende) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_ibfk1 FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_ibfk2 FOREIGN KEY (ref_cod_cliente_tipo) REFERENCES pmieducar.cliente_tipo(cod_cliente_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_ref_cod_cliente_tipo_fkey FOREIGN KEY (ref_cod_cliente_tipo) REFERENCES pmieducar.cliente_tipo(cod_cliente_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_ref_cod_exemplar_tipo_fkey FOREIGN KEY (ref_cod_exemplar_tipo) REFERENCES pmieducar.exemplar_tipo(cod_exemplar_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT codigo_curso_superior_1_fk FOREIGN KEY (codigo_curso_superior_1) REFERENCES modules.educacenso_curso_superior(id) ON DELETE SET NULL;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT codigo_curso_superior_2_fk FOREIGN KEY (codigo_curso_superior_2) REFERENCES modules.educacenso_curso_superior(id) ON DELETE SET NULL;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT codigo_curso_superior_3_fk FOREIGN KEY (codigo_curso_superior_3) REFERENCES modules.educacenso_curso_superior(id) ON DELETE SET NULL;

ALTER TABLE ONLY pmieducar.coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_nivel_ensino_fkey FOREIGN KEY (ref_cod_nivel_ensino) REFERENCES pmieducar.nivel_ensino(cod_nivel_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_tipo_ensino_fkey FOREIGN KEY (ref_cod_tipo_ensino) REFERENCES pmieducar.tipo_ensino(cod_tipo_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_tipo_regime_fkey FOREIGN KEY (ref_cod_tipo_regime) REFERENCES pmieducar.tipo_regime(cod_tipo_regime) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_usuario_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_usuario_exc_fk FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina_dependencia
    ADD CONSTRAINT disciplina_dependencia_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina_dependencia
    ADD CONSTRAINT disciplina_dependencia_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina_serie
    ADD CONSTRAINT disciplina_serie_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES pmieducar.disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina_serie
    ADD CONSTRAINT disciplina_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina_topico
    ADD CONSTRAINT disciplina_topico_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.disciplina_topico
    ADD CONSTRAINT disciplina_topico_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_tipo_dispensa_fkey FOREIGN KEY (ref_cod_tipo_dispensa) REFERENCES pmieducar.tipo_dispensa(cod_tipo_dispensa) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.distribuicao_uniforme
    ADD CONSTRAINT distribuicao_uniforme_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_codigo_indigena_fk FOREIGN KEY (codigo_lingua_indigena) REFERENCES modules.lingua_indigena_educacenso(id);

ALTER TABLE ONLY pmieducar.escola_complemento
    ADD CONSTRAINT escola_complemento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_complemento
    ADD CONSTRAINT escola_complemento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_cod_escola_rede_ensino_fkey FOREIGN KEY (ref_cod_escola_rede_ensino) REFERENCES pmieducar.escola_rede_ensino(cod_escola_rede_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_idpes_gestor_fk FOREIGN KEY (ref_idpes_gestor) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_idpes_secretario_escolar_fkey FOREIGN KEY (ref_idpes_secretario_escolar) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES modules.componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ref_cod_serie) REFERENCES pmieducar.escola_serie(ref_cod_escola, ref_cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.escola_usuario
    ADD CONSTRAINT escola_usuario_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);

ALTER TABLE ONLY pmieducar.escola_usuario
    ADD CONSTRAINT escola_usuario_ref_cod_usuario_fkey FOREIGN KEY (ref_cod_usuario) REFERENCES pmieducar.usuario(cod_usuario);

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_cod_exemplar_fkey FOREIGN KEY (ref_cod_exemplar) REFERENCES pmieducar.exemplar(cod_exemplar) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_usuario_devolucao_fkey FOREIGN KEY (ref_usuario_devolucao) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_fonte_fkey FOREIGN KEY (ref_cod_fonte) REFERENCES pmieducar.fonte(cod_fonte) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_motivo_baixa_fkey FOREIGN KEY (ref_cod_motivo_baixa) REFERENCES pmieducar.motivo_baixa(cod_motivo_baixa) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_situacao_fkey FOREIGN KEY (ref_cod_situacao) REFERENCES pmieducar.situacao(cod_situacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_cod_curso_disciplina FOREIGN KEY (ref_cod_curso_disciplina) REFERENCES pmieducar.disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.faltas
    ADD CONSTRAINT faltas_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.faltas
    ADD CONSTRAINT faltas_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT fk_abandono_tipo_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao);

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT fk_abandono_tipo_usuario_cad FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario);

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT fk_abandono_tipo_usuario_exc FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario);

ALTER TABLE ONLY pmieducar.distribuicao_uniforme
    ADD CONSTRAINT fk_distribuicao_uniforme_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT fk_matricula_abandono_tipo FOREIGN KEY (ref_cod_abandono_tipo) REFERENCES pmieducar.abandono_tipo(cod_abandono_tipo);

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT fk_ref_cod_biblioteca_cliente FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT fk_ref_cod_exemplar FOREIGN KEY (ref_cod_exemplar) REFERENCES pmieducar.exemplar(cod_exemplar) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT fk_servidor_pessoa FOREIGN KEY (cod_servidor) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT fk_turma_disciplina_dispensada FOREIGN KEY (ref_cod_disciplina_dispensada) REFERENCES modules.componente_curricular(id);

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funca_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_ref_cod_habilitacao_fkey FOREIGN KEY (ref_cod_habilitacao) REFERENCES pmieducar.habilitacao(cod_habilitacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.historico_disciplinas
    ADD CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey FOREIGN KEY (ref_ref_cod_aluno, ref_sequencial) REFERENCES pmieducar.historico_escolar(ref_cod_aluno, sequencial) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_escola_fkey1 FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_grade_curso_id_fkey FOREIGN KEY (historico_grade_curso_id) REFERENCES pmieducar.historico_grade_curso(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_cod_infra_comodo_funcao_fkey FOREIGN KEY (ref_cod_infra_comodo_funcao) REFERENCES pmieducar.infra_comodo_funcao(cod_infra_comodo_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_cod_infra_predio_fkey FOREIGN KEY (ref_cod_infra_predio) REFERENCES pmieducar.infra_predio(cod_infra_predio) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT instituicao_curso_superior_1_fk FOREIGN KEY (instituicao_curso_superior_1) REFERENCES modules.educacenso_ies(id);

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT instituicao_curso_superior_2_fk FOREIGN KEY (instituicao_curso_superior_2) REFERENCES modules.educacenso_ies(id);

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT instituicao_curso_superior_3_fk FOREIGN KEY (instituicao_curso_superior_3) REFERENCES modules.educacenso_ies(id);

ALTER TABLE ONLY pmieducar.instituicao_documentacao
    ADD CONSTRAINT instituicao_id_fkey FOREIGN KEY (instituicao_id) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_ref_idtlog_fkey FOREIGN KEY (ref_idtlog) REFERENCES urbano.tipo_logradouro(idtlog) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_cod_material_tipo_fkey FOREIGN KEY (ref_cod_material_tipo) REFERENCES pmieducar.material_tipo(cod_material_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_excessao
    ADD CONSTRAINT matricula_excessao_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_excessao
    ADD CONSTRAINT matricula_excessao_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula, ref_cod_turma, ref_sequencial) REFERENCES pmieducar.matricula_turma(ref_cod_matricula, ref_cod_turma, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_discipli_ref_cod_tipo_ocorrencia_disc_fkey FOREIGN KEY (ref_cod_tipo_ocorrencia_disciplinar) REFERENCES pmieducar.tipo_ocorrencia_disciplinar(cod_tipo_ocorrencia_disciplinar) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_cod_reserva_vaga_fkey FOREIGN KEY (ref_cod_reserva_vaga) REFERENCES pmieducar.reserva_vaga(cod_reserva_vaga) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_ref_cod_serie_fkey FOREIGN KEY (ref_ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES pmieducar.tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_cod_categoria_nivel_fkey FOREIGN KEY (ref_cod_categoria_nivel) REFERENCES pmieducar.categoria_nivel(cod_categoria_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_cod_nivel_anterior_fkey FOREIGN KEY (ref_cod_nivel_anterior) REFERENCES pmieducar.nivel(cod_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_curso_disciplina FOREIGN KEY (ref_cod_curso_disciplina) REFERENCES pmieducar.disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_ref_cod_tipo_avaliacao, ref_sequencial) REFERENCES pmieducar.tipo_avaliacao_valores(ref_cod_tipo_avaliacao, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.operador
    ADD CONSTRAINT operador_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.operador
    ADD CONSTRAINT operador_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_divida_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_multa_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_multa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.bloqueio_ano_letivo
    ADD CONSTRAINT pmieducar_bloqueio_ano_letivo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao);

ALTER TABLE ONLY pmieducar.projeto_aluno
    ADD CONSTRAINT pmieducar_projeto_aluno_ref_cod_aluno FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno);

ALTER TABLE ONLY pmieducar.projeto_aluno
    ADD CONSTRAINT pmieducar_projeto_aluno_ref_cod_projeto FOREIGN KEY (ref_cod_projeto) REFERENCES pmieducar.projeto(cod_projeto);

ALTER TABLE ONLY pmieducar.pre_requisito
    ADD CONSTRAINT pre_requisito_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.pre_requisito
    ADD CONSTRAINT pre_requisito_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_quadro_horario_fkey FOREIGN KEY (ref_cod_quadro_horario) REFERENCES pmieducar.quadro_horario(cod_quadro_horario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_quadro_horario_fkey FOREIGN KEY (ref_cod_quadro_horario) REFERENCES pmieducar.quadro_horario(cod_quadro_horario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_fkey FOREIGN KEY (ref_servidor, ref_cod_instituicao_servidor) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_fkey FOREIGN KEY (ref_servidor, ref_cod_instituicao_servidor) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_substituto_fkey FOREIGN KEY (ref_servidor_substituto, ref_cod_instituicao_substituto) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno);

ALTER TABLE ONLY pmieducar.dispensa_etapa
    ADD CONSTRAINT ref_cod_disciplina FOREIGN KEY (ref_cod_dispensa) REFERENCES pmieducar.dispensa_disciplina(cod_dispensa);

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES cadastro.pessoa(idpes);

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie);

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_turno_fkey FOREIGN KEY (ref_cod_turno) REFERENCES pmieducar.turma_turno(id);

ALTER TABLE ONLY pmieducar.relacao_categoria_acervo
    ADD CONSTRAINT relacao_categoria_acervo_categoria_id_fkey FOREIGN KEY (categoria_id) REFERENCES pmieducar.categoria_obra(id);

ALTER TABLE ONLY pmieducar.relacao_categoria_acervo
    ADD CONSTRAINT relacao_categoria_acervo_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo);

ALTER TABLE ONLY pmieducar.religiao
    ADD CONSTRAINT religiao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.religiao
    ADD CONSTRAINT religiao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_ref_cod_serie_fkey FOREIGN KEY (ref_ref_cod_serie, ref_ref_cod_escola) REFERENCES pmieducar.escola_serie(ref_cod_serie, ref_cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_ref_usuario_libera_fkey FOREIGN KEY (ref_usuario_libera) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_serie_destino_fkey FOREIGN KEY (ref_serie_destino) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_serie_origem_fkey FOREIGN KEY (ref_serie_origem) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_operador_fkey FOREIGN KEY (ref_cod_operador) REFERENCES pmieducar.operador(cod_operador) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_pre_requisito_fkey FOREIGN KEY (ref_cod_pre_requisito) REFERENCES pmieducar.pre_requisito(cod_pre_requisito) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_regra_avaliacao_diferenciada_fk FOREIGN KEY (regra_avaliacao_diferenciada_id) REFERENCES modules.regra_avaliacao(id) ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_regra_avaliacao_fk FOREIGN KEY (regra_avaliacao_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_curso_fk FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_serie_fk FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_cod_motivo_afastamento_fkey FOREIGN KEY (ref_cod_motivo_afastamento) REFERENCES pmieducar.motivo_afastamento(cod_motivo_afastamento) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_curso
    ADD CONSTRAINT servidor_curso_ref_cod_formacao_fkey FOREIGN KEY (ref_cod_formacao) REFERENCES pmieducar.servidor_formacao(cod_formacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES modules.componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_funcao
    ADD CONSTRAINT servidor_funcao_ref_cod_funcao_fkey FOREIGN KEY (ref_cod_funcao) REFERENCES pmieducar.funcao(cod_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_funcao
    ADD CONSTRAINT servidor_funcao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_ref_cod_subnivel_ FOREIGN KEY (ref_cod_subnivel) REFERENCES pmieducar.subnivel(cod_subnivel) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_ref_idesco_fkey FOREIGN KEY (ref_idesco) REFERENCES cadastro.escolaridade(idesco) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.servidor_titulo_concurso
    ADD CONSTRAINT servidor_titulo_concurso_ref_cod_formacao_fkey FOREIGN KEY (ref_cod_formacao) REFERENCES pmieducar.servidor_formacao(cod_formacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_cod_nivel_fkey FOREIGN KEY (ref_cod_nivel) REFERENCES pmieducar.nivel(cod_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_cod_subnivel_anterior_fkey FOREIGN KEY (ref_cod_subnivel_anterior) REFERENCES pmieducar.subnivel(cod_subnivel) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_avaliacao_valores
    ADD CONSTRAINT tipo_avaliacao_valores_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_cod_tipo_avaliacao) REFERENCES pmieducar.tipo_avaliacao(cod_tipo_avaliacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_usuario
    ADD CONSTRAINT tipo_usuario_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.tipo_usuario
    ADD CONSTRAINT tipo_usuario_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_matricula_entrada_fkey FOREIGN KEY (ref_cod_matricula_entrada) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_matricula_saida_fkey FOREIGN KEY (ref_cod_matricula_saida) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_transferencia_tipo_fkey FOREIGN KEY (ref_cod_transferencia_tipo) REFERENCES pmieducar.transferencia_tipo(cod_transferencia_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_escola_serie_muil FOREIGN KEY (ref_ref_cod_serie_mult, ref_ref_cod_escola_mult) REFERENCES pmieducar.escola_serie(ref_cod_serie, ref_cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma_modulo
    ADD CONSTRAINT turma_modulo_ref_cod_modulo_fkey FOREIGN KEY (ref_cod_modulo) REFERENCES pmieducar.modulo(cod_modulo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma_modulo
    ADD CONSTRAINT turma_modulo_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_infra_predio_comodo_fkey FOREIGN KEY (ref_cod_infra_predio_comodo) REFERENCES pmieducar.infra_predio_comodo(cod_infra_predio_comodo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_regente FOREIGN KEY (ref_cod_regente, ref_cod_instituicao_regente) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ref_cod_serie) REFERENCES pmieducar.escola_serie(ref_cod_escola, ref_cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma_tipo
    ADD CONSTRAINT turma_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma_tipo
    ADD CONSTRAINT turma_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_turma_tipo_fkey FOREIGN KEY (ref_cod_turma_tipo) REFERENCES pmieducar.turma_tipo(cod_turma_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_turno_id_fkey FOREIGN KEY (turma_turno_id) REFERENCES pmieducar.turma_turno(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_cod_usuario_fkey FOREIGN KEY (cod_usuario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES pmieducar.tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.funcionario_su
    ADD CONSTRAINT funcionario_su_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_cod_grupos_fkey FOREIGN KEY (ref_cod_grupos) REFERENCES pmiotopic.grupos(cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_cod_grupos_fkey FOREIGN KEY (ref_cod_grupos) REFERENCES pmiotopic.grupos(cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_pessoa_cadatro_fkey FOREIGN KEY (ref_pessoa_cad, ref_grupos_cad) REFERENCES pmiotopic.grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_pessoa_exclusao_fkey FOREIGN KEY (ref_pessoa_exc, ref_grupos_exc) REFERENCES pmiotopic.grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupos
    ADD CONSTRAINT grupos_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.grupos
    ADD CONSTRAINT grupos_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.participante
    ADD CONSTRAINT participante_ref_cod_reuniao_fkey FOREIGN KEY (ref_cod_reuniao) REFERENCES pmiotopic.reuniao(cod_reuniao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.participante
    ADD CONSTRAINT participante_ref_ref_idpes_fkey FOREIGN KEY (ref_ref_idpes, ref_ref_cod_grupos) REFERENCES pmiotopic.grupopessoa(ref_idpes, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.reuniao
    ADD CONSTRAINT reuniao_ref_moderador_fkey FOREIGN KEY (ref_moderador, ref_grupos_moderador) REFERENCES pmiotopic.grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.topicoreuniao
    ADD CONSTRAINT topicoreuniao_ref_cod_reuniao_fkey FOREIGN KEY (ref_cod_reuniao) REFERENCES pmiotopic.reuniao(cod_reuniao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY pmiotopic.topicoreuniao
    ADD CONSTRAINT topicoreuniao_ref_cod_topico_fkey FOREIGN KEY (ref_cod_topico) REFERENCES pmiotopic.topico(cod_topico) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_ref_cod_agenda_fkey FOREIGN KEY (ref_cod_agenda) REFERENCES portal.agenda(cod_agenda) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_ref_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_own_fkey FOREIGN KEY (ref_ref_cod_pessoa_own) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_ref_cod_agenda_fkey FOREIGN KEY (ref_cod_agenda) REFERENCES portal.agenda(cod_agenda) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_ibfk_1 FOREIGN KEY (ref_cod_compras_editais_editais) REFERENCES portal.compras_editais_editais(cod_compras_editais_editais) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_ibfk_2 FOREIGN KEY (ref_cod_compras_editais_empresa) REFERENCES portal.compras_editais_empresa(cod_compras_editais_empresa) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_ibfk_2 FOREIGN KEY (ref_cod_compras_licitacoes) REFERENCES portal.compras_licitacoes(cod_compras_licitacoes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_editais_empresa
    ADD CONSTRAINT compras_editais_empresa_ibfk_1 FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_funcionarios
    ADD CONSTRAINT compras_funcionarios_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_ibfk_1 FOREIGN KEY (ref_cod_compras_modalidade) REFERENCES portal.compras_modalidade(cod_compras_modalidade) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_ibfk_2 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_1 FOREIGN KEY (ref_equipe3) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_2 FOREIGN KEY (ref_pregoeiro) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_3 FOREIGN KEY (ref_equipe1) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_4 FOREIGN KEY (ref_equipe2) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_5 FOREIGN KEY (ref_cod_compras_final_pregao) REFERENCES portal.compras_final_pregao(cod_compras_final_pregao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_6 FOREIGN KEY (ref_cod_compras_licitacoes) REFERENCES portal.compras_licitacoes(cod_compras_licitacoes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT fk_to_setor_new FOREIGN KEY (ref_cod_setor_new) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.foto_portal
    ADD CONSTRAINT foto_portal_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.foto_portal
    ADD CONSTRAINT foto_portal_ibfk_2 FOREIGN KEY (ref_cod_credito) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT funcionario_ibfk_1 FOREIGN KEY (ref_cod_pessoa_fj) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT funcionario_ibfk_5 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_ref_cod_imagem_tipo_fkey FOREIGN KEY (ref_cod_imagem_tipo) REFERENCES portal.imagem_tipo(cod_imagem_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.intranet_segur_permissao_negada
    ADD CONSTRAINT intranet_segur_permissao_negada_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.jor_arquivo
    ADD CONSTRAINT jor_arquivo_ibfk_1 FOREIGN KEY (ref_cod_jor_edicao) REFERENCES portal.jor_edicao(cod_jor_edicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.jor_edicao
    ADD CONSTRAINT jor_edicao_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_email_conteudo
    ADD CONSTRAINT mailling_email_conteudo_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_2 FOREIGN KEY (ref_cod_mailling_email) REFERENCES portal.mailling_email(cod_mailling_email);

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_3 FOREIGN KEY (ref_cod_mailling_email_conteudo) REFERENCES portal.mailling_email_conteudo(cod_mailling_email_conteudo);

ALTER TABLE ONLY portal.mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_ibfk_1 FOREIGN KEY (ref_cod_mailling_email) REFERENCES portal.mailling_email(cod_mailling_email) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_ibfk_2 FOREIGN KEY (ref_cod_mailling_grupo) REFERENCES portal.mailling_grupo(cod_mailling_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_2 FOREIGN KEY (ref_cod_mailling_grupo) REFERENCES portal.mailling_grupo(cod_mailling_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_3 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.menu_funcionario
    ADD CONSTRAINT menu_funcionario_ibfk_1 FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.menu_funcionario
    ADD CONSTRAINT menu_funcionario_ibfk_2 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.menu_submenu
    ADD CONSTRAINT menu_submenu_ibfk_1 FOREIGN KEY (ref_cod_menu_menu) REFERENCES portal.menu_menu(cod_menu_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.not_portal
    ADD CONSTRAINT not_portal_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_ibfk_2 FOREIGN KEY (ref_cod_not_tipo) REFERENCES portal.not_tipo(cod_not_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.not_vinc_portal
    ADD CONSTRAINT not_vinc_portal_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.notificacao
    ADD CONSTRAINT notificacao_ref_cod_funcionario_fkey FOREIGN KEY (ref_cod_funcionario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.pessoa_atividade
    ADD CONSTRAINT pessoa_atividade_ibfk_1 FOREIGN KEY (ref_cod_ramo_atividade) REFERENCES portal.pessoa_ramo_atividade(cod_ramo_atividade) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_ibfk_1 FOREIGN KEY (ref_cod_pessoa_atividade) REFERENCES portal.pessoa_atividade(cod_pessoa_atividade) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_ibfk_2 FOREIGN KEY (ref_cod_pessoa_fj) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.portal_concurso
    ADD CONSTRAINT portal_concurso_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY portal.menu_menu
    ADD CONSTRAINT ref_cod_menu_pai_fk FOREIGN KEY (ref_cod_menu_pai) REFERENCES portal.menu_menu(cod_menu_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT bairro_idsetorbai_fk FOREIGN KEY (idsetorbai) REFERENCES public.setor_bai(idsetorbai);

ALTER TABLE ONLY public.bairro_regiao
    ADD CONSTRAINT bairro_regiao_ref_cod_regiao_fkey FOREIGN KEY (ref_cod_regiao) REFERENCES public.regiao(cod_regiao) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY public.bairro_regiao
    ADD CONSTRAINT bairro_regiao_ref_idbai_fkey FOREIGN KEY (ref_idbai) REFERENCES public.bairro(idbai) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_distrito FOREIGN KEY (iddis) REFERENCES public.distrito(iddis);

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.logradouro_fonetico
    ADD CONSTRAINT fk_logr_logr_fonetico FOREIGN KEY (idlog) REFERENCES public.logradouro(idlog);

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_logradouro_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_tipo_log FOREIGN KEY (idtlog) REFERENCES urbano.tipo_logradouro(idtlog);

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_municipiopai FOREIGN KEY (idmun_pai) REFERENCES public.municipio(idmun);

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_uf FOREIGN KEY (sigla_uf) REFERENCES public.uf(sigla_uf);

ALTER TABLE ONLY public.setor
    ADD CONSTRAINT fk_setor_idsetredir FOREIGN KEY (idsetredir) REFERENCES public.setor(idset) ON DELETE RESTRICT;

ALTER TABLE ONLY public.setor
    ADD CONSTRAINT fk_setor_idsetsub FOREIGN KEY (idsetsub) REFERENCES public.setor(idset) ON DELETE CASCADE;

ALTER TABLE ONLY public.uf
    ADD CONSTRAINT fk_uf_pais FOREIGN KEY (idpais) REFERENCES public.pais(idpais);

ALTER TABLE ONLY public.vila
    ADD CONSTRAINT fk_vila_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);

ALTER TABLE ONLY serieciasc.aluno_cod_aluno
    ADD CONSTRAINT aluno_cod_aluno_cod_aluno_fk FOREIGN KEY (cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.aluno_uniforme
    ADD CONSTRAINT aluno_uniforme_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_agua
    ADD CONSTRAINT escola_agua_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_energia
    ADD CONSTRAINT escola_energia_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_lingua_indigena
    ADD CONSTRAINT escola_lingua_indigena_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_lixo
    ADD CONSTRAINT escola_lixo_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_projeto
    ADD CONSTRAINT escola_projeto_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_regulamentacao
    ADD CONSTRAINT escola_regulamentacao_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY serieciasc.escola_sanitario
    ADD CONSTRAINT escola_sanitario_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_log_bairro_bai FOREIGN KEY (idbai) REFERENCES public.bairro(idbai);

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_log_bairro_cep_log FOREIGN KEY (cep, idlog) REFERENCES urbano.cep_logradouro(cep, idlog) ON DELETE CASCADE;

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_logradouro FOREIGN KEY (idlog) REFERENCES public.logradouro(idlog) ON DELETE CASCADE;

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;
