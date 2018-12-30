
--
-- TOC entry 6396 (class 2606 OID 9464913)
-- Name: fk_funcao_grp_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_funcao
    ADD CONSTRAINT fk_funcao_grp_funcao FOREIGN KEY (idfunc, idsis, idmen) REFERENCES acesso.funcao(idfunc, idsis, idmen);


--
-- TOC entry 6407 (class 2606 OID 9464918)
-- Name: fk_funcao_operacao_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.operacao_funcao
    ADD CONSTRAINT fk_funcao_operacao_funcao FOREIGN KEY (idfunc, idsis, idmen) REFERENCES acesso.funcao(idfunc, idsis, idmen);


--
-- TOC entry 6400 (class 2606 OID 9464923)
-- Name: fk_grp_fun_grp_operacao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_operacao
    ADD CONSTRAINT fk_grp_fun_grp_operacao FOREIGN KEY (idmen, idsis, idgrp, idfunc) REFERENCES acesso.grupo_funcao(idmen, idsis, idgrp, idfunc);


--
-- TOC entry 6395 (class 2606 OID 9464928)
-- Name: fk_grp_menu_grp_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_funcao
    ADD CONSTRAINT fk_grp_menu_grp_funcao FOREIGN KEY (idgrp, idsis, idmen) REFERENCES acesso.grupo_menu(idgrp, idsis, idmen);


--
-- TOC entry 6398 (class 2606 OID 9464933)
-- Name: fk_grp_sis_grp_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_menu
    ADD CONSTRAINT fk_grp_sis_grp_menu FOREIGN KEY (idsis, idgrp) REFERENCES acesso.grupo_sistema(idsis, idgrp);


--
-- TOC entry 6402 (class 2606 OID 9464938)
-- Name: fk_grupo_grupo_sistema; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_sistema
    ADD CONSTRAINT fk_grupo_grupo_sistema FOREIGN KEY (idgrp) REFERENCES acesso.grupo(idgrp);


--
-- TOC entry 6412 (class 2606 OID 9464943)
-- Name: fk_grupo_usuario_grupo; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.usuario_grupo
    ADD CONSTRAINT fk_grupo_usuario_grupo FOREIGN KEY (idgrp) REFERENCES acesso.grupo(idgrp);


--
-- TOC entry 6409 (class 2606 OID 9464948)
-- Name: fk_inst_pessoa_instituicao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.pessoa_instituicao
    ADD CONSTRAINT fk_inst_pessoa_instituicao FOREIGN KEY (idins) REFERENCES acesso.instituicao(idins);


--
-- TOC entry 6394 (class 2606 OID 9464953)
-- Name: fk_menu_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.funcao
    ADD CONSTRAINT fk_menu_funcao FOREIGN KEY (idmen, idsis) REFERENCES acesso.menu(idmen, idsis);


--
-- TOC entry 6397 (class 2606 OID 9464958)
-- Name: fk_menu_grp_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_menu
    ADD CONSTRAINT fk_menu_grp_menu FOREIGN KEY (idmen, idsis) REFERENCES acesso.menu(idmen, idsis);


--
-- TOC entry 6404 (class 2606 OID 9464963)
-- Name: fk_menu_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.menu
    ADD CONSTRAINT fk_menu_menu FOREIGN KEY (menu_idsis, menu_idmen) REFERENCES acesso.menu(idsis, idmen);


--
-- TOC entry 6399 (class 2606 OID 9464968)
-- Name: fk_oper_func_grp_oper; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_operacao
    ADD CONSTRAINT fk_oper_func_grp_oper FOREIGN KEY (idmen, idsis, idfunc, idope) REFERENCES acesso.operacao_funcao(idmen, idsis, idfunc, idope);


--
-- TOC entry 6406 (class 2606 OID 9464973)
-- Name: fk_operacao_operacao_funcao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.operacao_funcao
    ADD CONSTRAINT fk_operacao_operacao_funcao FOREIGN KEY (idope) REFERENCES acesso.operacao(idope);


--
-- TOC entry 6408 (class 2606 OID 9464978)
-- Name: fk_pes_pessoa_instituicao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.pessoa_instituicao
    ADD CONSTRAINT fk_pes_pessoa_instituicao FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6410 (class 2606 OID 9464983)
-- Name: fk_pessoa_usuario; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.usuario
    ADD CONSTRAINT fk_pessoa_usuario FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6401 (class 2606 OID 9464988)
-- Name: fk_sistema_grupo_sistema; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.grupo_sistema
    ADD CONSTRAINT fk_sistema_grupo_sistema FOREIGN KEY (idsis) REFERENCES acesso.sistema(idsis);


--
-- TOC entry 6403 (class 2606 OID 9464993)
-- Name: fk_sistema_menu; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.menu
    ADD CONSTRAINT fk_sistema_menu FOREIGN KEY (idsis) REFERENCES acesso.sistema(idsis);


--
-- TOC entry 6405 (class 2606 OID 9464998)
-- Name: fk_sistema_operacao; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.operacao
    ADD CONSTRAINT fk_sistema_operacao FOREIGN KEY (idsis) REFERENCES acesso.sistema(idsis);


--
-- TOC entry 6411 (class 2606 OID 9465003)
-- Name: fk_usuario_usuario_grupo; Type: FK CONSTRAINT; Schema: acesso; Owner: -
--

ALTER TABLE ONLY acesso.usuario_grupo
    ADD CONSTRAINT fk_usuario_usuario_grupo FOREIGN KEY (login) REFERENCES acesso.usuario(login);


--
-- TOC entry 6422 (class 2606 OID 9465008)
-- Name: fk_alterar_usuario_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_alterar_usuario_cardapio FOREIGN KEY (login_alteracao) REFERENCES acesso.usuario(login);


--
-- TOC entry 6415 (class 2606 OID 9465013)
-- Name: fk_baixa_guia_baixa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT fk_baixa_guia_baixa_produto FOREIGN KEY (idbai) REFERENCES alimentos.baixa_guia_remessa(idbai);


--
-- TOC entry 6437 (class 2606 OID 9465018)
-- Name: fk_calendario_evento; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.evento
    ADD CONSTRAINT fk_calendario_evento FOREIGN KEY (idcad) REFERENCES alimentos.calendario(idcad);


--
-- TOC entry 6482 (class 2606 OID 9465023)
-- Name: fk_calendario_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_calendario_unidade FOREIGN KEY (idcad) REFERENCES alimentos.calendario(idcad);


--
-- TOC entry 6454 (class 2606 OID 9465028)
-- Name: fk_cancelar_usuario_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_cancelar_usuario_guia_remessa FOREIGN KEY (login_cancelamento) REFERENCES acesso.usuario(login);


--
-- TOC entry 6424 (class 2606 OID 9465033)
-- Name: fk_cardapio_cardapio_faixa_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio_faixa_unidade
    ADD CONSTRAINT fk_cardapio_cardapio_faixa_unidade FOREIGN KEY (idcar) REFERENCES alimentos.cardapio(idcar);


--
-- TOC entry 6426 (class 2606 OID 9465038)
-- Name: fk_cardapio_cardapio_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio_produto
    ADD CONSTRAINT fk_cardapio_cardapio_produto FOREIGN KEY (idcar) REFERENCES alimentos.cardapio(idcar);


--
-- TOC entry 6428 (class 2606 OID 9465043)
-- Name: fk_cardapio_cardapio_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio_receita
    ADD CONSTRAINT fk_cardapio_cardapio_receita FOREIGN KEY (idcar) REFERENCES alimentos.cardapio(idcar);


--
-- TOC entry 6418 (class 2606 OID 9465048)
-- Name: fk_cliente_calendario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.calendario
    ADD CONSTRAINT fk_cliente_calendario FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6421 (class 2606 OID 9465053)
-- Name: fk_cliente_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_cliente_cardapio FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6434 (class 2606 OID 9465058)
-- Name: fk_cliente_contrato; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT fk_cliente_contrato FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6431 (class 2606 OID 9465063)
-- Name: fk_cliente_cpquimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.composto_quimico
    ADD CONSTRAINT fk_cliente_cpquimico FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6442 (class 2606 OID 9465068)
-- Name: fk_cliente_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.fornecedor
    ADD CONSTRAINT fk_cliente_fornecedor FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6440 (class 2606 OID 9465073)
-- Name: fk_cliente_grpatencao; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.faixa_etaria
    ADD CONSTRAINT fk_cliente_grpatencao FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6445 (class 2606 OID 9465078)
-- Name: fk_cliente_grpquimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.grupo_quimico
    ADD CONSTRAINT fk_cliente_grpquimico FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6453 (class 2606 OID 9465083)
-- Name: fk_cliente_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_cliente_guia_remessa FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6458 (class 2606 OID 9465088)
-- Name: fk_cliente_log_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.log_guia_remessa
    ADD CONSTRAINT fk_cliente_log_guia FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6471 (class 2606 OID 9465093)
-- Name: fk_cliente_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.receita
    ADD CONSTRAINT fk_cliente_receita FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6476 (class 2606 OID 9465098)
-- Name: fk_cliente_tpproduto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.tipo_produto
    ADD CONSTRAINT fk_cliente_tpproduto FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6477 (class 2606 OID 9465103)
-- Name: fk_cliente_tprefeicao; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.tipo_refeicao
    ADD CONSTRAINT fk_cliente_tprefeicao FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6478 (class 2606 OID 9465108)
-- Name: fk_cliente_tpunidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.tipo_unidade
    ADD CONSTRAINT fk_cliente_tpunidade FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6481 (class 2606 OID 9465113)
-- Name: fk_cliente_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_cliente_unidade FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6485 (class 2606 OID 9465118)
-- Name: fk_cliente_uniproduto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_produto
    ADD CONSTRAINT fk_cliente_uniproduto FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6436 (class 2606 OID 9465123)
-- Name: fk_contrato_contrato_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.contrato_produto
    ADD CONSTRAINT fk_contrato_contrato_produto FOREIGN KEY (idcon) REFERENCES alimentos.contrato(idcon);


--
-- TOC entry 6452 (class 2606 OID 9465128)
-- Name: fk_contrato_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_contrato_guia_remessa FOREIGN KEY (idcon) REFERENCES alimentos.contrato(idcon);


--
-- TOC entry 6439 (class 2606 OID 9465133)
-- Name: fk_cp_quimico_faixa_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.faixa_composto_quimico
    ADD CONSTRAINT fk_cp_quimico_faixa_cp_quimico FOREIGN KEY (idcom) REFERENCES alimentos.composto_quimico(idcom);


--
-- TOC entry 6451 (class 2606 OID 9465138)
-- Name: fk_emitir_usuario_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_emitir_usuario_guia_remessa FOREIGN KEY (login_emissao) REFERENCES acesso.usuario(login);


--
-- TOC entry 6484 (class 2606 OID 9465143)
-- Name: fk_faixa_etaria_unidade_faixa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_faixa_etaria
    ADD CONSTRAINT fk_faixa_etaria_unidade_faixa FOREIGN KEY (idfae) REFERENCES alimentos.faixa_etaria(idfae);


--
-- TOC entry 6438 (class 2606 OID 9465148)
-- Name: fk_faixa_faixa_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.faixa_composto_quimico
    ADD CONSTRAINT fk_faixa_faixa_cp_quimico FOREIGN KEY (idfae) REFERENCES alimentos.faixa_etaria(idfae);


--
-- TOC entry 6423 (class 2606 OID 9465153)
-- Name: fk_faixa_uni_cardapio_faixa_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio_faixa_unidade
    ADD CONSTRAINT fk_faixa_uni_cardapio_faixa_unidade FOREIGN KEY (idfeu) REFERENCES alimentos.unidade_faixa_etaria(idfeu);


--
-- TOC entry 6433 (class 2606 OID 9465158)
-- Name: fk_fornecedor_contrato; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT fk_fornecedor_contrato FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);


--
-- TOC entry 6450 (class 2606 OID 9465163)
-- Name: fk_fornecedor_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_fornecedor_guia_remessa FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);


--
-- TOC entry 6467 (class 2606 OID 9465168)
-- Name: fk_fornecedor_produto_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_fornecedor
    ADD CONSTRAINT fk_fornecedor_produto_fornecedor FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);


--
-- TOC entry 6444 (class 2606 OID 9465173)
-- Name: fk_fornecedor_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.fornecedor_unidade_atendida
    ADD CONSTRAINT fk_fornecedor_unidade FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);


--
-- TOC entry 6430 (class 2606 OID 9465178)
-- Name: fk_grupo_cp_quimico_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.composto_quimico
    ADD CONSTRAINT fk_grupo_cp_quimico_cp_quimico FOREIGN KEY (idgrpq) REFERENCES alimentos.grupo_quimico(idgrpq);


--
-- TOC entry 6456 (class 2606 OID 9465183)
-- Name: fk_guia_guia_remessa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa_produto
    ADD CONSTRAINT fk_guia_guia_remessa_produto FOREIGN KEY (idgui) REFERENCES alimentos.guia_remessa(idgui);


--
-- TOC entry 6414 (class 2606 OID 9465188)
-- Name: fk_guia_produto_baixa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT fk_guia_produto_baixa_produto FOREIGN KEY (idgup) REFERENCES alimentos.guia_remessa_produto(idgup);


--
-- TOC entry 6417 (class 2606 OID 9465193)
-- Name: fk_guia_remessa_baixa_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.baixa_guia_remessa
    ADD CONSTRAINT fk_guia_remessa_baixa_guia FOREIGN KEY (idgui) REFERENCES alimentos.guia_remessa(idgui);


--
-- TOC entry 6448 (class 2606 OID 9465198)
-- Name: fk_guia_remessa_guia_pro_diario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT fk_guia_remessa_guia_pro_diario FOREIGN KEY (idgui) REFERENCES alimentos.guia_remessa(idgui);


--
-- TOC entry 6420 (class 2606 OID 9465203)
-- Name: fk_incluir_usuario_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_incluir_usuario_cardapio FOREIGN KEY (login_inclusao) REFERENCES acesso.usuario(login);


--
-- TOC entry 6459 (class 2606 OID 9465208)
-- Name: fk_medidas_caseiras_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.medidas_caseiras
    ADD CONSTRAINT fk_medidas_caseiras_cliente FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6429 (class 2606 OID 9465213)
-- Name: fk_pessoa_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cliente
    ADD CONSTRAINT fk_pessoa_cliente FOREIGN KEY (idpes) REFERENCES alimentos.pessoa(idpes);


--
-- TOC entry 6441 (class 2606 OID 9465218)
-- Name: fk_pessoa_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.fornecedor
    ADD CONSTRAINT fk_pessoa_fornecedor FOREIGN KEY (idpes) REFERENCES alimentos.pessoa(idpes);


--
-- TOC entry 6480 (class 2606 OID 9465223)
-- Name: fk_pessoa_unidade_atend; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_pessoa_unidade_atend FOREIGN KEY (idpes) REFERENCES alimentos.pessoa(idpes);


--
-- TOC entry 6465 (class 2606 OID 9465228)
-- Name: fk_prod_cp_quimico_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_composto_quimico
    ADD CONSTRAINT fk_prod_cp_quimico_cp_quimico FOREIGN KEY (idcom) REFERENCES alimentos.composto_quimico(idcom);


--
-- TOC entry 6464 (class 2606 OID 9465233)
-- Name: fk_prod_cp_quimico_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_composto_quimico
    ADD CONSTRAINT fk_prod_cp_quimico_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6425 (class 2606 OID 9465238)
-- Name: fk_produto_cardapio_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio_produto
    ADD CONSTRAINT fk_produto_cardapio_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6463 (class 2606 OID 9465243)
-- Name: fk_produto_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_cliente FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6435 (class 2606 OID 9465248)
-- Name: fk_produto_contrato_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.contrato_produto
    ADD CONSTRAINT fk_produto_contrato_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6462 (class 2606 OID 9465253)
-- Name: fk_produto_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_fornecedor FOREIGN KEY (idfor) REFERENCES alimentos.fornecedor(idfor);


--
-- TOC entry 6447 (class 2606 OID 9465258)
-- Name: fk_produto_guia_pro_diario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT fk_produto_guia_pro_diario FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6455 (class 2606 OID 9465263)
-- Name: fk_produto_guia_remessa_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa_produto
    ADD CONSTRAINT fk_produto_guia_remessa_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6470 (class 2606 OID 9465268)
-- Name: fk_produto_medida_caseira_cliente; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_cliente FOREIGN KEY (idcli) REFERENCES alimentos.cliente(idcli);


--
-- TOC entry 6469 (class 2606 OID 9465273)
-- Name: fk_produto_medida_caseira_medidas; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_medidas FOREIGN KEY (idmedcas, idcli) REFERENCES alimentos.medidas_caseiras(idmedcas, idcli);


--
-- TOC entry 6468 (class 2606 OID 9465278)
-- Name: fk_produto_medida_caseira_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT fk_produto_medida_caseira_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6466 (class 2606 OID 9465283)
-- Name: fk_produto_produto_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto_fornecedor
    ADD CONSTRAINT fk_produto_produto_fornecedor FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6461 (class 2606 OID 9465288)
-- Name: fk_produto_tipo; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_tipo FOREIGN KEY (idtip) REFERENCES alimentos.tipo_produto(idtip);


--
-- TOC entry 6460 (class 2606 OID 9465293)
-- Name: fk_produto_unidade; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT fk_produto_unidade FOREIGN KEY (idunp, idcli) REFERENCES alimentos.unidade_produto(idunp, idcli);


--
-- TOC entry 6473 (class 2606 OID 9465298)
-- Name: fk_rec_cp_quimico_cp_quimico; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.receita_composto_quimico
    ADD CONSTRAINT fk_rec_cp_quimico_cp_quimico FOREIGN KEY (idcom) REFERENCES alimentos.composto_quimico(idcom);


--
-- TOC entry 6472 (class 2606 OID 9465303)
-- Name: fk_rec_cp_quimico_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.receita_composto_quimico
    ADD CONSTRAINT fk_rec_cp_quimico_receita FOREIGN KEY (idrec) REFERENCES alimentos.receita(idrec);


--
-- TOC entry 6475 (class 2606 OID 9465308)
-- Name: fk_rec_prod_produto; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.receita_produto
    ADD CONSTRAINT fk_rec_prod_produto FOREIGN KEY (idpro) REFERENCES alimentos.produto(idpro);


--
-- TOC entry 6474 (class 2606 OID 9465313)
-- Name: fk_rec_prod_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.receita_produto
    ADD CONSTRAINT fk_rec_prod_receita FOREIGN KEY (idrec) REFERENCES alimentos.receita(idrec);


--
-- TOC entry 6427 (class 2606 OID 9465318)
-- Name: fk_receita_cardapio_receita; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio_receita
    ADD CONSTRAINT fk_receita_cardapio_receita FOREIGN KEY (idrec) REFERENCES alimentos.receita(idrec);


--
-- TOC entry 6479 (class 2606 OID 9465323)
-- Name: fk_tipo_uni_uni_atendida; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT fk_tipo_uni_uni_atendida FOREIGN KEY (idtip) REFERENCES alimentos.tipo_unidade(idtip);


--
-- TOC entry 6419 (class 2606 OID 9465328)
-- Name: fk_tp_refeicao_cardapio; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT fk_tp_refeicao_cardapio FOREIGN KEY (idtre) REFERENCES alimentos.tipo_refeicao(idtre);


--
-- TOC entry 6449 (class 2606 OID 9465333)
-- Name: fk_uni_atend_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT fk_uni_atend_guia_remessa FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);


--
-- TOC entry 6483 (class 2606 OID 9465338)
-- Name: fk_uni_atend_uni_faixa_eta; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.unidade_faixa_etaria
    ADD CONSTRAINT fk_uni_atend_uni_faixa_eta FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);


--
-- TOC entry 6446 (class 2606 OID 9465343)
-- Name: fk_unidade_atendida_guia_pro_diario; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT fk_unidade_atendida_guia_pro_diario FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);


--
-- TOC entry 6443 (class 2606 OID 9465348)
-- Name: fk_unidade_fornecedor; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.fornecedor_unidade_atendida
    ADD CONSTRAINT fk_unidade_fornecedor FOREIGN KEY (iduni) REFERENCES alimentos.unidade_atendida(iduni);


--
-- TOC entry 6413 (class 2606 OID 9465353)
-- Name: fk_usuario_baixa_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT fk_usuario_baixa_guia FOREIGN KEY (login_baixa) REFERENCES acesso.usuario(login);


--
-- TOC entry 6416 (class 2606 OID 9465358)
-- Name: fk_usuario_baixa_guia_remessa; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.baixa_guia_remessa
    ADD CONSTRAINT fk_usuario_baixa_guia_remessa FOREIGN KEY (login_baixa) REFERENCES acesso.usuario(login);


--
-- TOC entry 6432 (class 2606 OID 9465363)
-- Name: fk_usuario_contrato; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT fk_usuario_contrato FOREIGN KEY (login) REFERENCES acesso.usuario(login);


--
-- TOC entry 6457 (class 2606 OID 9465368)
-- Name: fk_usuario_log_guia; Type: FK CONSTRAINT; Schema: alimentos; Owner: -
--

ALTER TABLE ONLY alimentos.log_guia_remessa
    ADD CONSTRAINT fk_usuario_log_guia FOREIGN KEY (login) REFERENCES acesso.usuario(login);


--
-- TOC entry 6496 (class 2606 OID 9474453)
-- Name: cartorio_cert_civil_inep_fk; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT cartorio_cert_civil_inep_fk FOREIGN KEY (cartorio_cert_civil_inep) REFERENCES cadastro.codigo_cartorio_inep(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6532 (class 2606 OID 9465373)
-- Name: fisica_foto_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_foto
    ADD CONSTRAINT fisica_foto_idpes_fkey FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6510 (class 2606 OID 9465378)
-- Name: fisica_ref_cod_religiao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fisica_ref_cod_religiao FOREIGN KEY (ref_cod_religiao) REFERENCES pmieducar.religiao(cod_religiao);


--
-- TOC entry 6535 (class 2606 OID 9465383)
-- Name: fisica_sangue_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_sangue
    ADD CONSTRAINT fisica_sangue_idpes_fkey FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6486 (class 2606 OID 9465388)
-- Name: fk_aviso_nome_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.aviso_nome
    ADD CONSTRAINT fk_aviso_nome_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes) ON DELETE RESTRICT;


--
-- TOC entry 6487 (class 2606 OID 9465393)
-- Name: fk_documento_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes);


--
-- TOC entry 6488 (class 2606 OID 9465398)
-- Name: fk_documento_orgao_emissor_rg; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_orgao_emissor_rg FOREIGN KEY (idorg_exp_rg) REFERENCES cadastro.orgao_emissor_rg(idorg_rg) ON DELETE RESTRICT;


--
-- TOC entry 6489 (class 2606 OID 9465403)
-- Name: fk_documento_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6490 (class 2606 OID 9465408)
-- Name: fk_documento_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6491 (class 2606 OID 9465413)
-- Name: fk_documento_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6492 (class 2606 OID 9465418)
-- Name: fk_documento_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6493 (class 2606 OID 9465423)
-- Name: fk_documento_uf_cart_trabalho; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_uf_cart_trabalho FOREIGN KEY (sigla_uf_cart_trabalho) REFERENCES public.uf(sigla_uf);


--
-- TOC entry 6494 (class 2606 OID 9465428)
-- Name: fk_documento_uf_cert_civil; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_uf_cert_civil FOREIGN KEY (sigla_uf_cert_civil) REFERENCES public.uf(sigla_uf);


--
-- TOC entry 6495 (class 2606 OID 9465433)
-- Name: fk_documento_uf_rg; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT fk_documento_uf_rg FOREIGN KEY (sigla_uf_exp_rg) REFERENCES public.uf(sigla_uf);


--
-- TOC entry 6497 (class 2606 OID 9465438)
-- Name: fk_endereco_externo_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6498 (class 2606 OID 9465443)
-- Name: fk_endereco_externo_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6499 (class 2606 OID 9465448)
-- Name: fk_endereco_externo_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6500 (class 2606 OID 9465453)
-- Name: fk_endereco_externo_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6501 (class 2606 OID 9465458)
-- Name: fk_endereco_externo_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6502 (class 2606 OID 9465463)
-- Name: fk_endereco_externo_tipo_log; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_tipo_log FOREIGN KEY (idtlog) REFERENCES urbano.tipo_logradouro(idtlog);


--
-- TOC entry 6503 (class 2606 OID 9465468)
-- Name: fk_endereco_externo_uf; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT fk_endereco_externo_uf FOREIGN KEY (sigla_uf) REFERENCES public.uf(sigla_uf);


--
-- TOC entry 6504 (class 2606 OID 9465473)
-- Name: fk_endereco_pes_cep_log_bai; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pes_cep_log_bai FOREIGN KEY (cep, idbai, idlog) REFERENCES urbano.cep_logradouro_bairro(cep, idbai, idlog) ON UPDATE CASCADE;


--
-- TOC entry 6505 (class 2606 OID 9465478)
-- Name: fk_endereco_pessoa_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6506 (class 2606 OID 9465483)
-- Name: fk_endereco_pessoa_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6507 (class 2606 OID 9465488)
-- Name: fk_endereco_pessoa_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6508 (class 2606 OID 9465493)
-- Name: fk_endereco_pessoa_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6509 (class 2606 OID 9465498)
-- Name: fk_endereco_pessoa_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT fk_endereco_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6529 (class 2606 OID 9465503)
-- Name: fk_fisica_cpf_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes) ON DELETE RESTRICT;


--
-- TOC entry 6528 (class 2606 OID 9465508)
-- Name: fk_fisica_cpf_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6527 (class 2606 OID 9465513)
-- Name: fk_fisica_cpf_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6526 (class 2606 OID 9465518)
-- Name: fk_fisica_cpf_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6525 (class 2606 OID 9465523)
-- Name: fk_fisica_cpf_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT fk_fisica_cpf_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6511 (class 2606 OID 9465528)
-- Name: fk_fisica_escolaridade; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_escolaridade FOREIGN KEY (idesco) REFERENCES cadastro.escolaridade(idesco);


--
-- TOC entry 6512 (class 2606 OID 9465533)
-- Name: fk_fisica_estado_civil; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_estado_civil FOREIGN KEY (ideciv) REFERENCES cadastro.estado_civil(ideciv);


--
-- TOC entry 6513 (class 2606 OID 9465538)
-- Name: fk_fisica_municipio; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_municipio FOREIGN KEY (idmun_nascimento) REFERENCES public.municipio(idmun);


--
-- TOC entry 6514 (class 2606 OID 9465543)
-- Name: fk_fisica_ocupacao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_ocupacao FOREIGN KEY (idocup) REFERENCES cadastro.ocupacao(idocup);


--
-- TOC entry 6515 (class 2606 OID 9465548)
-- Name: fk_fisica_pais; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pais FOREIGN KEY (idpais_estrangeiro) REFERENCES public.pais(idpais);


--
-- TOC entry 6516 (class 2606 OID 9465553)
-- Name: fk_fisica_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON DELETE RESTRICT;


--
-- TOC entry 6517 (class 2606 OID 9465558)
-- Name: fk_fisica_pessoa_conjuge; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_conjuge FOREIGN KEY (idpes_con) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6518 (class 2606 OID 9465563)
-- Name: fk_fisica_pessoa_mae; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_mae FOREIGN KEY (idpes_mae) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6519 (class 2606 OID 9465568)
-- Name: fk_fisica_pessoa_pai; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_pai FOREIGN KEY (idpes_pai) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6520 (class 2606 OID 9465573)
-- Name: fk_fisica_pessoa_responsavel; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_pessoa_responsavel FOREIGN KEY (idpes_responsavel) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6521 (class 2606 OID 9465578)
-- Name: fk_fisica_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6522 (class 2606 OID 9465583)
-- Name: fk_fisica_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6523 (class 2606 OID 9465588)
-- Name: fk_fisica_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6524 (class 2606 OID 9465593)
-- Name: fk_fisica_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT fk_fisica_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6540 (class 2606 OID 9465598)
-- Name: fk_fone_pessoa_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6539 (class 2606 OID 9465603)
-- Name: fk_fone_pessoa_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6538 (class 2606 OID 9465608)
-- Name: fk_fone_pessoa_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6537 (class 2606 OID 9465613)
-- Name: fk_fone_pessoa_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6536 (class 2606 OID 9465618)
-- Name: fk_fone_pessoa_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT fk_fone_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6541 (class 2606 OID 9465623)
-- Name: fk_funcionario_fisica; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_fisica FOREIGN KEY (idpes) REFERENCES cadastro.fisica(idpes);


--
-- TOC entry 6542 (class 2606 OID 9465628)
-- Name: fk_funcionario_instituicao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_instituicao FOREIGN KEY (idins) REFERENCES acesso.instituicao(idins);


--
-- TOC entry 6543 (class 2606 OID 9465633)
-- Name: fk_funcionario_setor; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_setor FOREIGN KEY (idset) REFERENCES public.setor(idset);


--
-- TOC entry 6544 (class 2606 OID 9465638)
-- Name: fk_funcionario_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6545 (class 2606 OID 9465643)
-- Name: fk_funcionario_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6546 (class 2606 OID 9465648)
-- Name: fk_funcionario_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6547 (class 2606 OID 9465653)
-- Name: fk_funcionario_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT fk_funcionario_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6549 (class 2606 OID 9465658)
-- Name: fk_hist_cartao_pes_cidadao; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.historico_cartao
    ADD CONSTRAINT fk_hist_cartao_pes_cidadao FOREIGN KEY (idpes_cidadao) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6548 (class 2606 OID 9465663)
-- Name: fk_hist_cartao_pes_emitiu; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.historico_cartao
    ADD CONSTRAINT fk_hist_cartao_pes_emitiu FOREIGN KEY (idpes_emitiu) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6554 (class 2606 OID 9465668)
-- Name: fk_juridica_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6553 (class 2606 OID 9465673)
-- Name: fk_juridica_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6552 (class 2606 OID 9465678)
-- Name: fk_juridica_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6551 (class 2606 OID 9465683)
-- Name: fk_juridica_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6550 (class 2606 OID 9465688)
-- Name: fk_juridica_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT fk_juridica_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6567 (class 2606 OID 9465693)
-- Name: fk_juridica_socio; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_juridica_socio FOREIGN KEY (idpes_juridica) REFERENCES cadastro.juridica(idpes);


--
-- TOC entry 6559 (class 2606 OID 9465698)
-- Name: fk_pessoa_fonetico_pessoa; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.pessoa_fonetico
    ADD CONSTRAINT fk_pessoa_fonetico_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6558 (class 2606 OID 9465703)
-- Name: fk_pessoa_pessoa_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_pessoa_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6557 (class 2606 OID 9465708)
-- Name: fk_pessoa_pessoa_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_pessoa_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6556 (class 2606 OID 9465713)
-- Name: fk_pessoa_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6555 (class 2606 OID 9465718)
-- Name: fk_pessoa_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT fk_pessoa_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6566 (class 2606 OID 9465723)
-- Name: fk_pessoa_socio; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_pessoa_socio FOREIGN KEY (idpes_fisica) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 7207 (class 2606 OID 9474360)
-- Name: fk_ref_sigla_uf; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.codigo_cartorio_inep
    ADD CONSTRAINT fk_ref_sigla_uf FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf);


--
-- TOC entry 6565 (class 2606 OID 9465728)
-- Name: fk_socio_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6564 (class 2606 OID 9465733)
-- Name: fk_socio_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6563 (class 2606 OID 9465738)
-- Name: fk_socio_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6562 (class 2606 OID 9465743)
-- Name: fk_socio_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT fk_socio_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6531 (class 2606 OID 9465748)
-- Name: pessoa_deficiencia_ref_cod_deficiencia_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_deficiencia
    ADD CONSTRAINT pessoa_deficiencia_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_deficiencia) REFERENCES cadastro.deficiencia(cod_deficiencia) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6530 (class 2606 OID 9465753)
-- Name: pessoa_deficiencia_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_deficiencia
    ADD CONSTRAINT pessoa_deficiencia_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6534 (class 2606 OID 9465758)
-- Name: pessoa_raca_ref_cod_deficiencia_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_raca
    ADD CONSTRAINT pessoa_raca_ref_cod_deficiencia_fkey FOREIGN KEY (ref_cod_raca) REFERENCES cadastro.raca(cod_raca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6533 (class 2606 OID 9465763)
-- Name: pessoa_raca_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.fisica_raca
    ADD CONSTRAINT pessoa_raca_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6561 (class 2606 OID 9465768)
-- Name: religiao_idpes_cad_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.religiao
    ADD CONSTRAINT religiao_idpes_cad_fkey FOREIGN KEY (idpes_cad) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6560 (class 2606 OID 9465773)
-- Name: religiao_idpes_exc_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.religiao
    ADD CONSTRAINT religiao_idpes_exc_fkey FOREIGN KEY (idpes_exc) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6589 (class 2606 OID 9465778)
-- Name: fk_campo_metadado_campo_consis; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT fk_campo_metadado_campo_consis FOREIGN KEY (idcam) REFERENCES consistenciacao.campo_consistenciacao(idcam);


--
-- TOC entry 6588 (class 2606 OID 9465783)
-- Name: fk_campo_metadado_metadado; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT fk_campo_metadado_metadado FOREIGN KEY (idmet) REFERENCES consistenciacao.metadado(idmet);


--
-- TOC entry 6587 (class 2606 OID 9465788)
-- Name: fk_campo_metadado_regra_campo; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT fk_campo_metadado_regra_campo FOREIGN KEY (idreg) REFERENCES consistenciacao.regra_campo(idreg);


--
-- TOC entry 6591 (class 2606 OID 9465793)
-- Name: fk_confrontacao_metadado; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.confrontacao
    ADD CONSTRAINT fk_confrontacao_metadado FOREIGN KEY (idmet) REFERENCES consistenciacao.metadado(idmet);


--
-- TOC entry 6590 (class 2606 OID 9465798)
-- Name: fk_confrontacao_pessoa_instituicao; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.confrontacao
    ADD CONSTRAINT fk_confrontacao_pessoa_instituicao FOREIGN KEY (idins, idpes) REFERENCES acesso.pessoa_instituicao(idins, idpes);


--
-- TOC entry 6593 (class 2606 OID 9465803)
-- Name: fk_hist_campo_campo_consist; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.historico_campo
    ADD CONSTRAINT fk_hist_campo_campo_consist FOREIGN KEY (idcam) REFERENCES consistenciacao.campo_consistenciacao(idcam) ON DELETE CASCADE;


--
-- TOC entry 6592 (class 2606 OID 9465808)
-- Name: fk_historico_campo_pessoa; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.historico_campo
    ADD CONSTRAINT fk_historico_campo_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes) ON DELETE CASCADE;


--
-- TOC entry 6599 (class 2606 OID 9465813)
-- Name: fk_inc_pessoa_possivel_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
    ADD CONSTRAINT fk_inc_pessoa_possivel_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;


--
-- TOC entry 6598 (class 2606 OID 9465818)
-- Name: fk_inc_pessoa_possivel_pessoa; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
    ADD CONSTRAINT fk_inc_pessoa_possivel_pessoa FOREIGN KEY (idpes) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6601 (class 2606 OID 9465823)
-- Name: fk_inc_tipo_inc_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
    ADD CONSTRAINT fk_inc_tipo_inc_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;


--
-- TOC entry 6600 (class 2606 OID 9465828)
-- Name: fk_inc_tipo_inc_tipo_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
    ADD CONSTRAINT fk_inc_tipo_inc_tipo_incoerencia FOREIGN KEY (id_tipo_inc) REFERENCES consistenciacao.tipo_incoerencia(id_tipo_inc);


--
-- TOC entry 6594 (class 2606 OID 9465833)
-- Name: fk_incoerencia_confrontacao; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia
    ADD CONSTRAINT fk_incoerencia_confrontacao FOREIGN KEY (idcon) REFERENCES consistenciacao.confrontacao(idcon);


--
-- TOC entry 6595 (class 2606 OID 9465838)
-- Name: fk_incoerencia_documento_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_documento
    ADD CONSTRAINT fk_incoerencia_documento_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;


--
-- TOC entry 6596 (class 2606 OID 9465843)
-- Name: fk_incoerencia_endereco_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_endereco
    ADD CONSTRAINT fk_incoerencia_endereco_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;


--
-- TOC entry 6597 (class 2606 OID 9465848)
-- Name: fk_incoerencia_fone_incoerencia; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.incoerencia_fone
    ADD CONSTRAINT fk_incoerencia_fone_incoerencia FOREIGN KEY (idinc) REFERENCES consistenciacao.incoerencia(idinc) ON DELETE CASCADE;


--
-- TOC entry 6602 (class 2606 OID 9465853)
-- Name: fk_metadado_fonte; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.metadado
    ADD CONSTRAINT fk_metadado_fonte FOREIGN KEY (idfon) REFERENCES consistenciacao.fonte(idfon);


--
-- TOC entry 6603 (class 2606 OID 9465858)
-- Name: fk_oco_reg_cam_regra_campo; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.ocorrencia_regra_campo
    ADD CONSTRAINT fk_oco_reg_cam_regra_campo FOREIGN KEY (idreg) REFERENCES consistenciacao.regra_campo(idreg);


--
-- TOC entry 6604 (class 2606 OID 9465863)
-- Name: fk_tipo_incoerencia_campo_consis; Type: FK CONSTRAINT; Schema: consistenciacao; Owner: -
--

ALTER TABLE ONLY consistenciacao.tipo_incoerencia
    ADD CONSTRAINT fk_tipo_incoerencia_campo_consis FOREIGN KEY (idcam) REFERENCES consistenciacao.campo_consistenciacao(idcam);


--
-- TOC entry 6605 (class 2606 OID 9465868)
-- Name: calendario_turma_calendario_dia_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.calendario_turma
    ADD CONSTRAINT calendario_turma_calendario_dia_fk FOREIGN KEY (calendario_ano_letivo_id, mes, dia) REFERENCES pmieducar.calendario_dia(ref_cod_calendario_ano_letivo, mes, dia) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 6607 (class 2606 OID 9465873)
-- Name: componente_curricular_ano_escolar_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.componente_curricular_ano_escolar
    ADD CONSTRAINT componente_curricular_ano_escolar_fk FOREIGN KEY (componente_curricular_id) REFERENCES modules.componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6606 (class 2606 OID 9465878)
-- Name: componente_curricular_area_conhecimento_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.componente_curricular
    ADD CONSTRAINT componente_curricular_area_conhecimento_fk FOREIGN KEY (area_conhecimento_id, instituicao_id) REFERENCES modules.area_conhecimento(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6609 (class 2606 OID 9465883)
-- Name: componente_curricular_turma_componente_curricular_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.componente_curricular_turma
    ADD CONSTRAINT componente_curricular_turma_componente_curricular_fkey FOREIGN KEY (componente_curricular_id) REFERENCES modules.componente_curricular(id) ON DELETE RESTRICT;


--
-- TOC entry 6608 (class 2606 OID 9465888)
-- Name: componente_curricular_turma_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.componente_curricular_turma
    ADD CONSTRAINT componente_curricular_turma_fkey FOREIGN KEY (turma_id) REFERENCES pmieducar.turma(cod_turma) ON DELETE CASCADE;


--
-- TOC entry 6610 (class 2606 OID 9465893)
-- Name: docente_licenciatura_ies_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.docente_licenciatura
    ADD CONSTRAINT docente_licenciatura_ies_fk FOREIGN KEY (ies_id) REFERENCES modules.educacenso_ies(id) ON DELETE RESTRICT;


--
-- TOC entry 6611 (class 2606 OID 9465898)
-- Name: educacenso_cod_aluno_cod_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.educacenso_cod_aluno
    ADD CONSTRAINT educacenso_cod_aluno_cod_aluno_fk FOREIGN KEY (cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON DELETE CASCADE;


--
-- TOC entry 6612 (class 2606 OID 9465903)
-- Name: educacenso_cod_escola_cod_escola_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.educacenso_cod_escola
    ADD CONSTRAINT educacenso_cod_escola_cod_escola_fk FOREIGN KEY (cod_escola) REFERENCES pmieducar.escola(cod_escola) ON DELETE CASCADE;


--
-- TOC entry 6613 (class 2606 OID 9465908)
-- Name: educacenso_cod_turma_cod_turma_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.educacenso_cod_turma
    ADD CONSTRAINT educacenso_cod_turma_cod_turma_fk FOREIGN KEY (cod_turma) REFERENCES pmieducar.turma(cod_turma) ON DELETE CASCADE;


--
-- TOC entry 6615 (class 2606 OID 9465913)
-- Name: empresa_transporte_escolar_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.empresa_transporte_escolar
    ADD CONSTRAINT empresa_transporte_escolar_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes);


--
-- TOC entry 6614 (class 2606 OID 9465918)
-- Name: empresa_transporte_escolar_ref_resp_idpes_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.empresa_transporte_escolar
    ADD CONSTRAINT empresa_transporte_escolar_ref_resp_idpes_fkey FOREIGN KEY (ref_resp_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6617 (class 2606 OID 9465923)
-- Name: etapas_curso_educacenso_curso_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.etapas_curso_educacenso
    ADD CONSTRAINT etapas_curso_educacenso_curso_fk FOREIGN KEY (curso_id) REFERENCES pmieducar.curso(cod_curso);


--
-- TOC entry 6616 (class 2606 OID 9465928)
-- Name: etapas_curso_educacenso_etapa_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.etapas_curso_educacenso
    ADD CONSTRAINT etapas_curso_educacenso_etapa_fk FOREIGN KEY (etapa_id) REFERENCES modules.etapas_educacenso(id);


--
-- TOC entry 6618 (class 2606 OID 9465933)
-- Name: falta_componente_curricular_falta_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.falta_componente_curricular
    ADD CONSTRAINT falta_componente_curricular_falta_aluno_fk FOREIGN KEY (falta_aluno_id) REFERENCES modules.falta_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6619 (class 2606 OID 9465938)
-- Name: falta_geral_falta_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.falta_geral
    ADD CONSTRAINT falta_geral_falta_aluno_fk FOREIGN KEY (falta_aluno_id) REFERENCES modules.falta_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6620 (class 2606 OID 9465943)
-- Name: ficha_medica_aluno_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.ficha_medica_aluno
    ADD CONSTRAINT ficha_medica_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6637 (class 2606 OID 9465948)
-- Name: fk_ponto_cep_log_bai; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.ponto_transporte_escolar
    ADD CONSTRAINT fk_ponto_cep_log_bai FOREIGN KEY (idbai, idlog, cep) REFERENCES urbano.cep_logradouro_bairro(idbai, idlog, cep);


--
-- TOC entry 6622 (class 2606 OID 9465953)
-- Name: itinerario_transporte_escolar_ref_cod_rota_transporte_escolar_f; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.itinerario_transporte_escolar
    ADD CONSTRAINT itinerario_transporte_escolar_ref_cod_rota_transporte_escolar_f FOREIGN KEY (ref_cod_rota_transporte_escolar) REFERENCES modules.rota_transporte_escolar(cod_rota_transporte_escolar);


--
-- TOC entry 6623 (class 2606 OID 9465958)
-- Name: media_geral_nota_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.media_geral
    ADD CONSTRAINT media_geral_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6624 (class 2606 OID 9465963)
-- Name: moradia_aluno_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.moradia_aluno
    ADD CONSTRAINT moradia_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6629 (class 2606 OID 9465968)
-- Name: moradia_aluno_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.nota_exame
    ADD CONSTRAINT moradia_aluno_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6626 (class 2606 OID 9465973)
-- Name: motorista_ref_cod_empresa_transporte_escolar_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.motorista
    ADD CONSTRAINT motorista_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar) REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6625 (class 2606 OID 9465978)
-- Name: motorista_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.motorista
    ADD CONSTRAINT motorista_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6628 (class 2606 OID 9465983)
-- Name: nota_componente_curricular_media_nota_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.nota_componente_curricular_media
    ADD CONSTRAINT nota_componente_curricular_media_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6627 (class 2606 OID 9465988)
-- Name: nota_componente_curricular_nota_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.nota_componente_curricular
    ADD CONSTRAINT nota_componente_curricular_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6630 (class 2606 OID 9465993)
-- Name: nota_nota_geral_nota_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.nota_geral
    ADD CONSTRAINT nota_nota_geral_nota_aluno_fk FOREIGN KEY (nota_aluno_id) REFERENCES modules.nota_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6631 (class 2606 OID 9465998)
-- Name: parecer_componente_curricular_parecer_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.parecer_componente_curricular
    ADD CONSTRAINT parecer_componente_curricular_parecer_aluno_fk FOREIGN KEY (parecer_aluno_id) REFERENCES modules.parecer_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6632 (class 2606 OID 9466003)
-- Name: parecer_geral_parecer_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.parecer_geral
    ADD CONSTRAINT parecer_geral_parecer_aluno_fk FOREIGN KEY (parecer_aluno_id) REFERENCES modules.parecer_aluno(id) ON DELETE CASCADE;


--
-- TOC entry 6636 (class 2606 OID 9466008)
-- Name: pessoa_transporte_ref_cod_ponto_transporte_escolar_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_cod_ponto_transporte_escolar_fkey FOREIGN KEY (ref_cod_ponto_transporte_escolar) REFERENCES modules.ponto_transporte_escolar(cod_ponto_transporte_escolar);


--
-- TOC entry 6635 (class 2606 OID 9466013)
-- Name: pessoa_transporte_ref_cod_rota_transporte_escolar_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_cod_rota_transporte_escolar_fkey FOREIGN KEY (ref_cod_rota_transporte_escolar) REFERENCES modules.rota_transporte_escolar(cod_rota_transporte_escolar);


--
-- TOC entry 6634 (class 2606 OID 9466018)
-- Name: pessoa_transporte_ref_idpes_destino_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_idpes_destino_fkey FOREIGN KEY (ref_idpes_destino) REFERENCES cadastro.juridica(idpes);


--
-- TOC entry 6633 (class 2606 OID 9466023)
-- Name: pessoa_transporte_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6621 (class 2606 OID 9466028)
-- Name: ponto_transporte_escolar_ref_cod_veiculo_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.itinerario_transporte_escolar
    ADD CONSTRAINT ponto_transporte_escolar_ref_cod_veiculo_fkey FOREIGN KEY (ref_cod_veiculo) REFERENCES modules.veiculo(cod_veiculo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6642 (class 2606 OID 9466033)
-- Name: professor_turma_disciplina_componente_curricular_id_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.professor_turma_disciplina
    ADD CONSTRAINT professor_turma_disciplina_componente_curricular_id_fk FOREIGN KEY (componente_curricular_id) REFERENCES modules.componente_curricular(id) MATCH FULL ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6641 (class 2606 OID 9466038)
-- Name: professor_turma_disciplina_professor_turma_id_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.professor_turma_disciplina
    ADD CONSTRAINT professor_turma_disciplina_professor_turma_id_fk FOREIGN KEY (professor_turma_id) REFERENCES modules.professor_turma(id) MATCH FULL ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6640 (class 2606 OID 9466043)
-- Name: professor_turma_servidor_id_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_servidor_id_fk FOREIGN KEY (servidor_id, instituicao_id) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) MATCH FULL ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6639 (class 2606 OID 9466048)
-- Name: professor_turma_turma_id_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_turma_id_fk FOREIGN KEY (turma_id) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6638 (class 2606 OID 9474700)
-- Name: professor_turma_turma_turno_id_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_turma_turno_id_fk FOREIGN KEY (turno_id) REFERENCES pmieducar.turma_turno(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7206 (class 2606 OID 9469025)
-- Name: ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.config_movimento_geral
    ADD CONSTRAINT ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie);


--
-- TOC entry 6646 (class 2606 OID 9466053)
-- Name: regra_avaliacao_formula_media_formula_media_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_formula_media_formula_media_fk FOREIGN KEY (formula_media_id, instituicao_id) REFERENCES modules.formula_media(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6645 (class 2606 OID 9466058)
-- Name: regra_avaliacao_formula_media_formula_recuperacao_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_formula_media_formula_recuperacao_fk FOREIGN KEY (formula_recuperacao_id, instituicao_id) REFERENCES modules.formula_media(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6647 (class 2606 OID 9466063)
-- Name: regra_avaliacao_regra_avaliacao_recuperacao_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao_recuperacao
    ADD CONSTRAINT regra_avaliacao_regra_avaliacao_recuperacao_fk FOREIGN KEY (regra_avaliacao_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7208 (class 2606 OID 9474585)
-- Name: regra_avaliacao_serie_ano_fk_regra_avaliacao_diferenciada_id; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_fk_regra_avaliacao_diferenciada_id FOREIGN KEY (regra_avaliacao_diferenciada_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7209 (class 2606 OID 9474580)
-- Name: regra_avaliacao_serie_ano_fk_regra_avaliacao_id; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_fk_regra_avaliacao_id FOREIGN KEY (regra_avaliacao_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7210 (class 2606 OID 9474575)
-- Name: regra_avaliacao_serie_ano_fk_serie_id; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_fk_serie_id FOREIGN KEY (serie_id) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6644 (class 2606 OID 9466068)
-- Name: regra_avaliacao_tabela_arredondamento_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_tabela_arredondamento_fk FOREIGN KEY (tabela_arredondamento_id, instituicao_id) REFERENCES modules.tabela_arredondamento(id, instituicao_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6643 (class 2606 OID 9469012)
-- Name: regra_diferenciada_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_diferenciada_fk FOREIGN KEY (regra_diferenciada_id) REFERENCES modules.regra_avaliacao(id);


--
-- TOC entry 6649 (class 2606 OID 9466073)
-- Name: rota_transporte_escolar_ref_cod_empresa_transporte_escolar_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.rota_transporte_escolar
    ADD CONSTRAINT rota_transporte_escolar_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar) REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6648 (class 2606 OID 9466078)
-- Name: rota_transporte_escolar_ref_idpes_destino_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.rota_transporte_escolar
    ADD CONSTRAINT rota_transporte_escolar_ref_idpes_destino_fkey FOREIGN KEY (ref_idpes_destino) REFERENCES cadastro.juridica(idpes);


--
-- TOC entry 6650 (class 2606 OID 9466083)
-- Name: tabela_arredondamento_tabela_arredondamento_valor_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.tabela_arredondamento_valor
    ADD CONSTRAINT tabela_arredondamento_tabela_arredondamento_valor_fk FOREIGN KEY (tabela_arredondamento_id) REFERENCES modules.tabela_arredondamento(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6651 (class 2606 OID 9466088)
-- Name: transporte_aluno_aluno_fk; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.transporte_aluno
    ADD CONSTRAINT transporte_aluno_aluno_fk FOREIGN KEY (aluno_id) REFERENCES pmieducar.aluno(cod_aluno) ON DELETE CASCADE;


--
-- TOC entry 6652 (class 2606 OID 9466093)
-- Name: uniforme_aluno_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.uniforme_aluno
    ADD CONSTRAINT uniforme_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6654 (class 2606 OID 9466098)
-- Name: veiculo_ref_cod_empresa_transporte_escolar_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.veiculo
    ADD CONSTRAINT veiculo_ref_cod_empresa_transporte_escolar_fkey FOREIGN KEY (ref_cod_empresa_transporte_escolar) REFERENCES modules.empresa_transporte_escolar(cod_empresa_transporte_escolar);


--
-- TOC entry 6653 (class 2606 OID 9466103)
-- Name: veiculo_ref_cod_tipo_veiculo_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.veiculo
    ADD CONSTRAINT veiculo_ref_cod_tipo_veiculo_fkey FOREIGN KEY (ref_cod_tipo_veiculo) REFERENCES modules.tipo_veiculo(cod_tipo_veiculo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6658 (class 2606 OID 9466108)
-- Name: acao_governo_arquivo_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6657 (class 2606 OID 9466113)
-- Name: acao_governo_arquivo_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6660 (class 2606 OID 9466118)
-- Name: acao_governo_categoria_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6659 (class 2606 OID 9466123)
-- Name: acao_governo_categoria_ref_cod_categoria_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_ref_cod_categoria_fkey FOREIGN KEY (ref_cod_categoria) REFERENCES pmiacoes.categoria(cod_categoria) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6665 (class 2606 OID 9466128)
-- Name: acao_governo_foto_portal_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6664 (class 2606 OID 9466133)
-- Name: acao_governo_foto_portal_ref_cod_foto_portal_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_cod_foto_portal_fkey FOREIGN KEY (ref_cod_foto_portal) REFERENCES portal.foto_portal(cod_foto_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6663 (class 2606 OID 9466138)
-- Name: acao_governo_foto_portal_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6662 (class 2606 OID 9466143)
-- Name: acao_governo_foto_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6661 (class 2606 OID 9466148)
-- Name: acao_governo_foto_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6668 (class 2606 OID 9466153)
-- Name: acao_governo_noticia_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6667 (class 2606 OID 9466158)
-- Name: acao_governo_noticia_ref_cod_not_portal_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_cod_not_portal_fkey FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6666 (class 2606 OID 9466163)
-- Name: acao_governo_noticia_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6656 (class 2606 OID 9466168)
-- Name: acao_governo_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo
    ADD CONSTRAINT acao_governo_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6655 (class 2606 OID 9466173)
-- Name: acao_governo_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo
    ADD CONSTRAINT acao_governo_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6671 (class 2606 OID 9466178)
-- Name: acao_governo_setor_ref_cod_acao_governo_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_cod_acao_governo_fkey FOREIGN KEY (ref_cod_acao_governo) REFERENCES pmiacoes.acao_governo(cod_acao_governo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6670 (class 2606 OID 9466183)
-- Name: acao_governo_setor_ref_cod_setor_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_cod_setor_fkey FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6669 (class 2606 OID 9466188)
-- Name: acao_governo_setor_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6673 (class 2606 OID 9466193)
-- Name: categoria_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.categoria
    ADD CONSTRAINT categoria_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6672 (class 2606 OID 9466198)
-- Name: categoria_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.categoria
    ADD CONSTRAINT categoria_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6675 (class 2606 OID 9466203)
-- Name: secretaria_responsavel_ref_cod_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_ref_cod_funcionario_cad_fkey FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6674 (class 2606 OID 9466208)
-- Name: secretaria_responsavel_ref_cod_setor_fkey; Type: FK CONSTRAINT; Schema: pmiacoes; Owner: -
--

ALTER TABLE ONLY pmiacoes.secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_ref_cod_setor_fkey FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6678 (class 2606 OID 9466213)
-- Name: acontecimento_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6702 (class 2606 OID 9466218)
-- Name: acontecimento_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6677 (class 2606 OID 9466223)
-- Name: acontecimento_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6701 (class 2606 OID 9466228)
-- Name: acontecimento_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
    ADD CONSTRAINT acontecimento_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6687 (class 2606 OID 9466233)
-- Name: fk_to_imagem_ico; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT fk_to_imagem_ico FOREIGN KEY (ref_cod_ico) REFERENCES portal.imagem(cod_imagem) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6686 (class 2606 OID 9466238)
-- Name: fk_to_tutor; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT fk_to_tutor FOREIGN KEY (ref_cod_tutormenu) REFERENCES pmicontrolesis.tutormenu(cod_tutormenu);


--
-- TOC entry 6679 (class 2606 OID 9466243)
-- Name: foto_evento_ibfk_1; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.foto_evento
    ADD CONSTRAINT foto_evento_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6681 (class 2606 OID 9466248)
-- Name: foto_vinc_ref_cod_acontecimento_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.foto_vinc
    ADD CONSTRAINT foto_vinc_ref_cod_acontecimento_fkey FOREIGN KEY (ref_cod_acontecimento) REFERENCES pmicontrolesis.acontecimento(cod_acontecimento);


--
-- TOC entry 6680 (class 2606 OID 9466253)
-- Name: foto_vinc_ref_cod_foto_evento_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.foto_vinc
    ADD CONSTRAINT foto_vinc_ref_cod_foto_evento_fkey FOREIGN KEY (ref_cod_foto_evento) REFERENCES pmicontrolesis.foto_evento(cod_foto_evento);


--
-- TOC entry 6683 (class 2606 OID 9466258)
-- Name: itinerario_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.itinerario
    ADD CONSTRAINT itinerario_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6682 (class 2606 OID 9466263)
-- Name: itinerario_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.itinerario
    ADD CONSTRAINT itinerario_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6689 (class 2606 OID 9466268)
-- Name: menu_portal_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.menu_portal
    ADD CONSTRAINT menu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6688 (class 2606 OID 9466273)
-- Name: menu_portal_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.menu_portal
    ADD CONSTRAINT menu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6685 (class 2606 OID 9466278)
-- Name: menu_ref_cod_menu_pai_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT menu_ref_cod_menu_pai_fkey FOREIGN KEY (ref_cod_menu_pai) REFERENCES pmicontrolesis.menu(cod_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6684 (class 2606 OID 9466283)
-- Name: menu_ref_cod_menu_submenu_fkey; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT menu_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6691 (class 2606 OID 9466288)
-- Name: portais_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.portais
    ADD CONSTRAINT portais_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6690 (class 2606 OID 9466293)
-- Name: portais_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.portais
    ADD CONSTRAINT portais_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6693 (class 2606 OID 9466298)
-- Name: servicos_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.servicos
    ADD CONSTRAINT servicos_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6692 (class 2606 OID 9466303)
-- Name: servicos_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.servicos
    ADD CONSTRAINT servicos_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6695 (class 2606 OID 9466308)
-- Name: sistema_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.sistema
    ADD CONSTRAINT sistema_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6694 (class 2606 OID 9466313)
-- Name: sistema_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.sistema
    ADD CONSTRAINT sistema_ref_funcionario_exc_fk FOREIGN KEY (ref_cod_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6698 (class 2606 OID 9466318)
-- Name: submenu_portal_ref_cod_menu_portal_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_ref_cod_menu_portal_fk FOREIGN KEY (ref_cod_menu_portal) REFERENCES pmicontrolesis.menu_portal(cod_menu_portal);


--
-- TOC entry 6697 (class 2606 OID 9466323)
-- Name: submenu_portal_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6705 (class 2606 OID 9466328)
-- Name: submenu_portal_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6696 (class 2606 OID 9466333)
-- Name: submenu_portal_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6704 (class 2606 OID 9466338)
-- Name: submenu_portal_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT submenu_portal_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6700 (class 2606 OID 9466343)
-- Name: telefones_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.telefones
    ADD CONSTRAINT telefones_ref_funcionario_cad_fk FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6699 (class 2606 OID 9466348)
-- Name: telefones_ref_funcionario_exc_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.telefones
    ADD CONSTRAINT telefones_ref_funcionario_exc_fk FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj);


--
-- TOC entry 6676 (class 2606 OID 9466353)
-- Name: tipo_acontecimento_ref_funcionario_cad_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT tipo_acontecimento_ref_funcionario_cad_fk FOREIGN KEY (ref_cod_tipo_acontecimento) REFERENCES pmicontrolesis.tipo_acontecimento(cod_tipo_acontecimento);


--
-- TOC entry 6703 (class 2606 OID 9466358)
-- Name: topo_portal_ref_cod_menu_portal_fk; Type: FK CONSTRAINT; Schema: pmicontrolesis; Owner: -
--

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT topo_portal_ref_cod_menu_portal_fk FOREIGN KEY (ref_cod_menu_portal) REFERENCES pmicontrolesis.menu_portal(cod_menu_portal);


--
-- TOC entry 6709 (class 2606 OID 9466363)
-- Name: diaria_ref_cod_diaria_grupo_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_cod_diaria_grupo_fkey FOREIGN KEY (ref_cod_diaria_grupo) REFERENCES pmidrh.diaria_grupo(cod_diaria_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6708 (class 2606 OID 9466368)
-- Name: diaria_ref_cod_setor; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_cod_setor FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor);


--
-- TOC entry 6707 (class 2606 OID 9466373)
-- Name: diaria_ref_funcionario_cadastro_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_funcionario_cadastro_fkey FOREIGN KEY (ref_funcionario_cadastro) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6706 (class 2606 OID 9466378)
-- Name: diaria_ref_funcionario_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_ref_funcionario_fkey FOREIGN KEY (ref_funcionario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6711 (class 2606 OID 9466383)
-- Name: diaria_valores_ref_cod_diaria_grupo_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.diaria_valores
    ADD CONSTRAINT diaria_valores_ref_cod_diaria_grupo_fkey FOREIGN KEY (ref_cod_diaria_grupo) REFERENCES pmidrh.diaria_grupo(cod_diaria_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6710 (class 2606 OID 9466388)
-- Name: diaria_valores_ref_funcionario_cadastro_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.diaria_valores
    ADD CONSTRAINT diaria_valores_ref_funcionario_cadastro_fkey FOREIGN KEY (ref_funcionario_cadastro) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6715 (class 2606 OID 9466393)
-- Name: fk_setor_pai; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT fk_setor_pai FOREIGN KEY (ref_cod_setor) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6714 (class 2606 OID 9466398)
-- Name: fk_to_idpes_resp; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT fk_to_idpes_resp FOREIGN KEY (ref_idpes_resp) REFERENCES cadastro.fisica(idpes);


--
-- TOC entry 6713 (class 2606 OID 9466403)
-- Name: setor_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT setor_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6712 (class 2606 OID 9466408)
-- Name: setor_ref_cod_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmidrh; Owner: -
--

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT setor_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6728 (class 2606 OID 9466413)
-- Name: acervo_acervo_assunto_ref_cod_acervo_assunto_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_ref_cod_acervo_assunto_fkey FOREIGN KEY (ref_cod_acervo_assunto) REFERENCES pmieducar.acervo_assunto(cod_acervo_assunto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6727 (class 2606 OID 9466418)
-- Name: acervo_acervo_assunto_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6730 (class 2606 OID 9466423)
-- Name: acervo_acervo_autor_ref_cod_acervo_autor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_ref_cod_acervo_autor_fkey FOREIGN KEY (ref_cod_acervo_autor) REFERENCES pmieducar.acervo_autor(cod_acervo_autor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6729 (class 2606 OID 9466428)
-- Name: acervo_acervo_autor_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6733 (class 2606 OID 9466433)
-- Name: acervo_assunto_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6732 (class 2606 OID 9466438)
-- Name: acervo_assunto_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6731 (class 2606 OID 9466443)
-- Name: acervo_assunto_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6736 (class 2606 OID 9466448)
-- Name: acervo_autor_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_autor_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6735 (class 2606 OID 9466453)
-- Name: acervo_autor_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_autor_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6739 (class 2606 OID 9466458)
-- Name: acervo_colecao_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6738 (class 2606 OID 9466463)
-- Name: acervo_colecao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6737 (class 2606 OID 9466468)
-- Name: acervo_colecao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6744 (class 2606 OID 9466473)
-- Name: acervo_editora_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6743 (class 2606 OID 9466478)
-- Name: acervo_editora_ref_idtlog_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_idtlog_fkey FOREIGN KEY (ref_idtlog) REFERENCES urbano.tipo_logradouro(idtlog) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6742 (class 2606 OID 9466483)
-- Name: acervo_editora_ref_sigla_uf_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_sigla_uf_fkey FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6741 (class 2606 OID 9466488)
-- Name: acervo_editora_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6740 (class 2606 OID 9466493)
-- Name: acervo_editora_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6747 (class 2606 OID 9466498)
-- Name: acervo_idioma_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6746 (class 2606 OID 9466503)
-- Name: acervo_idioma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6745 (class 2606 OID 9466508)
-- Name: acervo_idioma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6726 (class 2606 OID 9466513)
-- Name: acervo_ref_cod_acervo_colecao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_colecao_fkey FOREIGN KEY (ref_cod_acervo_colecao) REFERENCES pmieducar.acervo_colecao(cod_acervo_colecao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6725 (class 2606 OID 9466518)
-- Name: acervo_ref_cod_acervo_editora_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_editora_fkey FOREIGN KEY (ref_cod_acervo_editora) REFERENCES pmieducar.acervo_editora(cod_acervo_editora) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6724 (class 2606 OID 9466523)
-- Name: acervo_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6723 (class 2606 OID 9466528)
-- Name: acervo_ref_cod_acervo_idioma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_acervo_idioma_fkey FOREIGN KEY (ref_cod_acervo_idioma) REFERENCES pmieducar.acervo_idioma(cod_acervo_idioma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6722 (class 2606 OID 9466533)
-- Name: acervo_ref_cod_biblioteca; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6734 (class 2606 OID 9466538)
-- Name: acervo_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6721 (class 2606 OID 9466543)
-- Name: acervo_ref_cod_exemplar_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_cod_exemplar_tipo_fkey FOREIGN KEY (ref_cod_exemplar_tipo) REFERENCES pmieducar.exemplar_tipo(cod_exemplar_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6720 (class 2606 OID 9466548)
-- Name: acervo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6719 (class 2606 OID 9466553)
-- Name: acervo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6752 (class 2606 OID 9466558)
-- Name: aluno_aluno_beneficio_aluno_beneficio_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
    ADD CONSTRAINT aluno_aluno_beneficio_aluno_beneficio_fk FOREIGN KEY (aluno_beneficio_id) REFERENCES pmieducar.aluno_beneficio(cod_aluno_beneficio);


--
-- TOC entry 6751 (class 2606 OID 9466563)
-- Name: aluno_aluno_beneficio_aluno_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
    ADD CONSTRAINT aluno_aluno_beneficio_aluno_fk FOREIGN KEY (aluno_id) REFERENCES pmieducar.aluno(cod_aluno);


--
-- TOC entry 6754 (class 2606 OID 9466568)
-- Name: aluno_beneficio_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6753 (class 2606 OID 9466573)
-- Name: aluno_beneficio_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6755 (class 2606 OID 9466578)
-- Name: aluno_historico_altura_peso_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_historico_altura_peso
    ADD CONSTRAINT aluno_historico_altura_peso_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno);


--
-- TOC entry 6750 (class 2606 OID 9466583)
-- Name: aluno_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6749 (class 2606 OID 9466588)
-- Name: aluno_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6748 (class 2606 OID 9466593)
-- Name: aluno_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6757 (class 2606 OID 9466598)
-- Name: ano_letivo_modulo_ref_cod_modulo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_ref_cod_modulo_fkey FOREIGN KEY (ref_cod_modulo) REFERENCES pmieducar.modulo(cod_modulo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6756 (class 2606 OID 9466603)
-- Name: ano_letivo_modulo_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ano) REFERENCES pmieducar.escola_ano_letivo(ref_cod_escola, ano) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6759 (class 2606 OID 9466608)
-- Name: auditoria_falta_componente_di_ref_cod_componente_curricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa
    ADD CONSTRAINT auditoria_falta_componente_di_ref_cod_componente_curricula_fkey FOREIGN KEY (ref_cod_componente_curricular) REFERENCES modules.componente_curricular(id);


--
-- TOC entry 6758 (class 2606 OID 9466613)
-- Name: auditoria_falta_componente_dispensa_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa
    ADD CONSTRAINT auditoria_falta_componente_dispensa_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula);


--
-- TOC entry 6761 (class 2606 OID 9466618)
-- Name: auditoria_nota_dispensa_ref_cod_componente_curricular_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
    ADD CONSTRAINT auditoria_nota_dispensa_ref_cod_componente_curricular_fkey FOREIGN KEY (ref_cod_componente_curricular) REFERENCES modules.componente_curricular(id);


--
-- TOC entry 6760 (class 2606 OID 9466623)
-- Name: auditoria_nota_dispensa_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
    ADD CONSTRAINT auditoria_nota_dispensa_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula);


--
-- TOC entry 6764 (class 2606 OID 9466628)
-- Name: avaliacao_desempenho_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6763 (class 2606 OID 9466633)
-- Name: avaliacao_desempenho_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6762 (class 2606 OID 9466638)
-- Name: avaliacao_desempenho_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6767 (class 2606 OID 9466643)
-- Name: biblioteca_dia_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.biblioteca_dia
    ADD CONSTRAINT biblioteca_dia_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6768 (class 2606 OID 9466648)
-- Name: biblioteca_feriados_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.biblioteca_feriados
    ADD CONSTRAINT biblioteca_feriados_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6766 (class 2606 OID 9466653)
-- Name: biblioteca_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.biblioteca
    ADD CONSTRAINT biblioteca_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6765 (class 2606 OID 9466658)
-- Name: biblioteca_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.biblioteca
    ADD CONSTRAINT biblioteca_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6769 (class 2606 OID 9466663)
-- Name: biblioteca_usuario_ref_cod_biblioteca_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.biblioteca_usuario
    ADD CONSTRAINT biblioteca_usuario_ref_cod_biblioteca_fk FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6771 (class 2606 OID 9466668)
-- Name: bloqueio_lancamento_faltas_notas_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.bloqueio_lancamento_faltas_notas
    ADD CONSTRAINT bloqueio_lancamento_faltas_notas_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6774 (class 2606 OID 9466673)
-- Name: calendario_ano_letivo_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6773 (class 2606 OID 9466678)
-- Name: calendario_ano_letivo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6772 (class 2606 OID 9466683)
-- Name: calendario_ano_letivo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6776 (class 2606 OID 9466688)
-- Name: calendario_anotacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6775 (class 2606 OID 9466693)
-- Name: calendario_anotacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6782 (class 2606 OID 9466698)
-- Name: calendario_dia_anotacao_ref_cod_calendario_anotacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_ref_cod_calendario_anotacao_fkey FOREIGN KEY (ref_cod_calendario_anotacao) REFERENCES pmieducar.calendario_anotacao(cod_calendario_anotacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6781 (class 2606 OID 9466703)
-- Name: calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_ref_cod_calendario_ano_letivo, ref_mes, ref_dia) REFERENCES pmieducar.calendario_dia(ref_cod_calendario_ano_letivo, mes, dia) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6785 (class 2606 OID 9466708)
-- Name: calendario_dia_motivo_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6784 (class 2606 OID 9466713)
-- Name: calendario_dia_motivo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6783 (class 2606 OID 9466718)
-- Name: calendario_dia_motivo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6780 (class 2606 OID 9466723)
-- Name: calendario_dia_ref_cod_calendario_ano_letivo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_cod_calendario_ano_letivo) REFERENCES pmieducar.calendario_ano_letivo(cod_calendario_ano_letivo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6779 (class 2606 OID 9466728)
-- Name: calendario_dia_ref_cod_calendario_dia_motivo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_cod_calendario_dia_motivo_fkey FOREIGN KEY (ref_cod_calendario_dia_motivo) REFERENCES pmieducar.calendario_dia_motivo(cod_calendario_dia_motivo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6778 (class 2606 OID 9466733)
-- Name: calendario_dia_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6777 (class 2606 OID 9466738)
-- Name: calendario_dia_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6790 (class 2606 OID 9466743)
-- Name: candidato_reserva_vaga_ref_cod_escola; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT candidato_reserva_vaga_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);


--
-- TOC entry 6792 (class 2606 OID 9466748)
-- Name: categoria_nivel_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.categoria_nivel
    ADD CONSTRAINT categoria_nivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6791 (class 2606 OID 9466753)
-- Name: categoria_nivel_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.categoria_nivel
    ADD CONSTRAINT categoria_nivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6795 (class 2606 OID 9466758)
-- Name: cliente_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6794 (class 2606 OID 9466763)
-- Name: cliente_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6793 (class 2606 OID 9466768)
-- Name: cliente_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6799 (class 2606 OID 9466773)
-- Name: cliente_suspensao_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6798 (class 2606 OID 9466778)
-- Name: cliente_suspensao_ref_cod_motivo_suspensao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_cod_motivo_suspensao_fkey FOREIGN KEY (ref_cod_motivo_suspensao) REFERENCES pmieducar.motivo_suspensao(cod_motivo_suspensao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6797 (class 2606 OID 9466783)
-- Name: cliente_suspensao_ref_usuario_libera_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_usuario_libera_fkey FOREIGN KEY (ref_usuario_libera) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6796 (class 2606 OID 9466788)
-- Name: cliente_suspensao_ref_usuario_suspende_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_ref_usuario_suspende_fkey FOREIGN KEY (ref_usuario_suspende) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6805 (class 2606 OID 9466793)
-- Name: cliente_tipo_cliente_ibfk1; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_ibfk1 FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6804 (class 2606 OID 9466798)
-- Name: cliente_tipo_cliente_ibfk2; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_ibfk2 FOREIGN KEY (ref_cod_cliente_tipo) REFERENCES pmieducar.cliente_tipo(cod_cliente_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6807 (class 2606 OID 9466803)
-- Name: cliente_tipo_exemplar_tipo_ref_cod_cliente_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_ref_cod_cliente_tipo_fkey FOREIGN KEY (ref_cod_cliente_tipo) REFERENCES pmieducar.cliente_tipo(cod_cliente_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6806 (class 2606 OID 9466808)
-- Name: cliente_tipo_exemplar_tipo_ref_cod_exemplar_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_ref_cod_exemplar_tipo_fkey FOREIGN KEY (ref_cod_exemplar_tipo) REFERENCES pmieducar.exemplar_tipo(cod_exemplar_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6802 (class 2606 OID 9466813)
-- Name: cliente_tipo_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6801 (class 2606 OID 9466818)
-- Name: cliente_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6800 (class 2606 OID 9466823)
-- Name: cliente_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7026 (class 2606 OID 9469036)
-- Name: codigo_curso_superior_1_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT codigo_curso_superior_1_fk FOREIGN KEY (codigo_curso_superior_1) REFERENCES modules.educacenso_curso_superior(id) ON DELETE SET NULL;


--
-- TOC entry 7027 (class 2606 OID 9469041)
-- Name: codigo_curso_superior_2_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT codigo_curso_superior_2_fk FOREIGN KEY (codigo_curso_superior_2) REFERENCES modules.educacenso_curso_superior(id) ON DELETE SET NULL;


--
-- TOC entry 7028 (class 2606 OID 9469046)
-- Name: codigo_curso_superior_3_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT codigo_curso_superior_3_fk FOREIGN KEY (codigo_curso_superior_3) REFERENCES modules.educacenso_curso_superior(id) ON DELETE SET NULL;


--
-- TOC entry 6809 (class 2606 OID 9466843)
-- Name: coffebreak_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6808 (class 2606 OID 9466848)
-- Name: coffebreak_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6815 (class 2606 OID 9466853)
-- Name: curso_ref_cod_instituicao_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6814 (class 2606 OID 9466858)
-- Name: curso_ref_cod_nivel_ensino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_nivel_ensino_fkey FOREIGN KEY (ref_cod_nivel_ensino) REFERENCES pmieducar.nivel_ensino(cod_nivel_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6813 (class 2606 OID 9466863)
-- Name: curso_ref_cod_tipo_ensino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_tipo_ensino_fkey FOREIGN KEY (ref_cod_tipo_ensino) REFERENCES pmieducar.tipo_ensino(cod_tipo_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6812 (class 2606 OID 9466868)
-- Name: curso_ref_cod_tipo_regime_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_tipo_regime_fkey FOREIGN KEY (ref_cod_tipo_regime) REFERENCES pmieducar.tipo_regime(cod_tipo_regime) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6811 (class 2606 OID 9466873)
-- Name: curso_ref_cod_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_cod_usuario_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6810 (class 2606 OID 9466878)
-- Name: curso_ref_usuario_exc_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_ref_usuario_exc_fk FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6820 (class 2606 OID 9466883)
-- Name: disciplina_dependencia_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina_dependencia
    ADD CONSTRAINT disciplina_dependencia_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6819 (class 2606 OID 9466888)
-- Name: disciplina_dependencia_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina_dependencia
    ADD CONSTRAINT disciplina_dependencia_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6818 (class 2606 OID 9466893)
-- Name: disciplina_ref_cod_curso; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6817 (class 2606 OID 9466898)
-- Name: disciplina_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6816 (class 2606 OID 9466903)
-- Name: disciplina_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6822 (class 2606 OID 9466908)
-- Name: disciplina_serie_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina_serie
    ADD CONSTRAINT disciplina_serie_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES pmieducar.disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6821 (class 2606 OID 9466913)
-- Name: disciplina_serie_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina_serie
    ADD CONSTRAINT disciplina_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6824 (class 2606 OID 9466918)
-- Name: disciplina_topico_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina_topico
    ADD CONSTRAINT disciplina_topico_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6823 (class 2606 OID 9466923)
-- Name: disciplina_topico_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.disciplina_topico
    ADD CONSTRAINT disciplina_topico_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6828 (class 2606 OID 9466928)
-- Name: dispensa_disciplina_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6827 (class 2606 OID 9466938)
-- Name: dispensa_disciplina_ref_cod_tipo_dispensa_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_cod_tipo_dispensa_fkey FOREIGN KEY (ref_cod_tipo_dispensa) REFERENCES pmieducar.tipo_dispensa(cod_tipo_dispensa) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6826 (class 2606 OID 9466943)
-- Name: dispensa_disciplina_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6825 (class 2606 OID 9466948)
-- Name: dispensa_disciplina_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT dispensa_disciplina_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6831 (class 2606 OID 9466953)
-- Name: distribuicao_uniforme_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.distribuicao_uniforme
    ADD CONSTRAINT distribuicao_uniforme_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6842 (class 2606 OID 9466958)
-- Name: escola_ano_letivo_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6841 (class 2606 OID 9466963)
-- Name: escola_ano_letivo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6840 (class 2606 OID 9466968)
-- Name: escola_ano_letivo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6832 (class 2606 OID 9466973)
-- Name: escola_codigo_indigena_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_codigo_indigena_fk FOREIGN KEY (codigo_lingua_indigena) REFERENCES modules.lingua_indigena_educacenso(id);


--
-- TOC entry 6844 (class 2606 OID 9466978)
-- Name: escola_complemento_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_complemento
    ADD CONSTRAINT escola_complemento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6843 (class 2606 OID 9466983)
-- Name: escola_complemento_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_complemento
    ADD CONSTRAINT escola_complemento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6848 (class 2606 OID 9466988)
-- Name: escola_curso_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6847 (class 2606 OID 9466993)
-- Name: escola_curso_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6846 (class 2606 OID 9466998)
-- Name: escola_curso_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6845 (class 2606 OID 9467003)
-- Name: escola_curso_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6851 (class 2606 OID 9467008)
-- Name: escola_localizacao_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6850 (class 2606 OID 9467013)
-- Name: escola_localizacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6849 (class 2606 OID 9467018)
-- Name: escola_localizacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6853 (class 2606 OID 9467023)
-- Name: escola_rede_ensino_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6852 (class 2606 OID 9467028)
-- Name: escola_rede_ensino_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6833 (class 2606 OID 9467038)
-- Name: escola_ref_cod_escola_rede_ensino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_cod_escola_rede_ensino_fkey FOREIGN KEY (ref_cod_escola_rede_ensino) REFERENCES pmieducar.escola_rede_ensino(cod_escola_rede_ensino) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6834 (class 2606 OID 9467043)
-- Name: escola_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6835 (class 2606 OID 9467048)
-- Name: escola_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6836 (class 2606 OID 9467053)
-- Name: escola_ref_idpes_gestor_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_idpes_gestor_fk FOREIGN KEY (ref_idpes_gestor) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6837 (class 2606 OID 9467058)
-- Name: escola_ref_idpes_secretario_escolar_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_idpes_secretario_escolar_fkey FOREIGN KEY (ref_idpes_secretario_escolar) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6838 (class 2606 OID 9467063)
-- Name: escola_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6839 (class 2606 OID 9467068)
-- Name: escola_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6859 (class 2606 OID 9467073)
-- Name: escola_serie_disciplina_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES modules.componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6858 (class 2606 OID 9467078)
-- Name: escola_serie_disciplina_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ref_cod_serie) REFERENCES pmieducar.escola_serie(ref_cod_escola, ref_cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6857 (class 2606 OID 9467083)
-- Name: escola_serie_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6856 (class 2606 OID 9467088)
-- Name: escola_serie_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6855 (class 2606 OID 9467093)
-- Name: escola_serie_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6854 (class 2606 OID 9467098)
-- Name: escola_serie_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6861 (class 2606 OID 9467103)
-- Name: escola_usuario_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_usuario
    ADD CONSTRAINT escola_usuario_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);


--
-- TOC entry 6860 (class 2606 OID 9467108)
-- Name: escola_usuario_ref_cod_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.escola_usuario
    ADD CONSTRAINT escola_usuario_ref_cod_usuario_fkey FOREIGN KEY (ref_cod_usuario) REFERENCES pmieducar.usuario(cod_usuario);


--
-- TOC entry 6871 (class 2606 OID 9467113)
-- Name: exemplar_emprestimo_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6870 (class 2606 OID 9467118)
-- Name: exemplar_emprestimo_ref_cod_exemplar_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_cod_exemplar_fkey FOREIGN KEY (ref_cod_exemplar) REFERENCES pmieducar.exemplar(cod_exemplar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6869 (class 2606 OID 9467123)
-- Name: exemplar_emprestimo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6868 (class 2606 OID 9467128)
-- Name: exemplar_emprestimo_ref_usuario_devolucao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_ref_usuario_devolucao_fkey FOREIGN KEY (ref_usuario_devolucao) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6867 (class 2606 OID 9467133)
-- Name: exemplar_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6866 (class 2606 OID 9467138)
-- Name: exemplar_ref_cod_fonte_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_fonte_fkey FOREIGN KEY (ref_cod_fonte) REFERENCES pmieducar.fonte(cod_fonte) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6865 (class 2606 OID 9467143)
-- Name: exemplar_ref_cod_motivo_baixa_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_motivo_baixa_fkey FOREIGN KEY (ref_cod_motivo_baixa) REFERENCES pmieducar.motivo_baixa(cod_motivo_baixa) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6864 (class 2606 OID 9467148)
-- Name: exemplar_ref_cod_situacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_cod_situacao_fkey FOREIGN KEY (ref_cod_situacao) REFERENCES pmieducar.situacao(cod_situacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6863 (class 2606 OID 9467153)
-- Name: exemplar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6862 (class 2606 OID 9467158)
-- Name: exemplar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6874 (class 2606 OID 9467163)
-- Name: exemplar_tipo_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6873 (class 2606 OID 9467168)
-- Name: exemplar_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6872 (class 2606 OID 9467173)
-- Name: exemplar_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6878 (class 2606 OID 9467178)
-- Name: falta_aluno_ref_cod_curso_disciplina; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_cod_curso_disciplina FOREIGN KEY (ref_cod_curso_disciplina) REFERENCES pmieducar.disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6877 (class 2606 OID 9467183)
-- Name: falta_aluno_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6876 (class 2606 OID 9467188)
-- Name: falta_aluno_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6875 (class 2606 OID 9467193)
-- Name: falta_aluno_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6886 (class 2606 OID 9467198)
-- Name: falta_atraso_compensado_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6885 (class 2606 OID 9467203)
-- Name: falta_atraso_compensado_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6884 (class 2606 OID 9467208)
-- Name: falta_atraso_compensado_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6883 (class 2606 OID 9467213)
-- Name: falta_atraso_compensado_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6882 (class 2606 OID 9467218)
-- Name: falta_atraso_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6881 (class 2606 OID 9467223)
-- Name: falta_atraso_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6880 (class 2606 OID 9467228)
-- Name: falta_atraso_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6879 (class 2606 OID 9467233)
-- Name: falta_atraso_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6888 (class 2606 OID 9467238)
-- Name: faltas_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.faltas
    ADD CONSTRAINT faltas_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6887 (class 2606 OID 9467243)
-- Name: faltas_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.faltas
    ADD CONSTRAINT faltas_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6718 (class 2606 OID 9467248)
-- Name: fk_abandono_tipo_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT fk_abandono_tipo_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao);


--
-- TOC entry 6717 (class 2606 OID 9467253)
-- Name: fk_abandono_tipo_usuario_cad; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT fk_abandono_tipo_usuario_cad FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario);


--
-- TOC entry 6716 (class 2606 OID 9467258)
-- Name: fk_abandono_tipo_usuario_exc; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT fk_abandono_tipo_usuario_exc FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario);


--
-- TOC entry 6830 (class 2606 OID 9467263)
-- Name: fk_distribuicao_uniforme_escola; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.distribuicao_uniforme
    ADD CONSTRAINT fk_distribuicao_uniforme_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);


--
-- TOC entry 6935 (class 2606 OID 9467268)
-- Name: fk_matricula_abandono_tipo; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT fk_matricula_abandono_tipo FOREIGN KEY (ref_cod_abandono_tipo) REFERENCES pmieducar.abandono_tipo(cod_abandono_tipo);


--
-- TOC entry 6803 (class 2606 OID 9467273)
-- Name: fk_ref_cod_biblioteca_cliente; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT fk_ref_cod_biblioteca_cliente FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7002 (class 2606 OID 9467278)
-- Name: fk_ref_cod_exemplar; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT fk_ref_cod_exemplar FOREIGN KEY (ref_cod_exemplar) REFERENCES pmieducar.exemplar(cod_exemplar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7019 (class 2606 OID 9467283)
-- Name: fk_servidor_pessoa; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT fk_servidor_pessoa FOREIGN KEY (cod_servidor) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 7081 (class 2606 OID 9467288)
-- Name: fk_turma_disciplina_dispensada; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT fk_turma_disciplina_dispensada FOREIGN KEY (ref_cod_disciplina_dispensada) REFERENCES modules.componente_curricular(id);


--
-- TOC entry 6891 (class 2606 OID 9467293)
-- Name: fonte_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6890 (class 2606 OID 9467298)
-- Name: fonte_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6889 (class 2606 OID 9467303)
-- Name: fonte_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6894 (class 2606 OID 9467308)
-- Name: funca_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funca_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6893 (class 2606 OID 9467313)
-- Name: funcao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6892 (class 2606 OID 9467318)
-- Name: funcao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6899 (class 2606 OID 9467323)
-- Name: habilitacao_curso_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6898 (class 2606 OID 9467328)
-- Name: habilitacao_curso_ref_cod_habilitacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_ref_cod_habilitacao_fkey FOREIGN KEY (ref_cod_habilitacao) REFERENCES pmieducar.habilitacao(cod_habilitacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6897 (class 2606 OID 9467333)
-- Name: habilitacao_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6896 (class 2606 OID 9467338)
-- Name: habilitacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6895 (class 2606 OID 9467343)
-- Name: habilitacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6900 (class 2606 OID 9467348)
-- Name: historico_disciplinas_ref_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_disciplinas
    ADD CONSTRAINT historico_disciplinas_ref_ref_cod_aluno_fkey FOREIGN KEY (ref_ref_cod_aluno, ref_sequencial) REFERENCES pmieducar.historico_escolar(ref_cod_aluno, sequencial) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6906 (class 2606 OID 9467353)
-- Name: historico_escolar_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6905 (class 2606 OID 9467358)
-- Name: historico_escolar_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);


--
-- TOC entry 6904 (class 2606 OID 9467363)
-- Name: historico_escolar_ref_cod_escola_fkey1; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_cod_escola_fkey1 FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);


--
-- TOC entry 6903 (class 2606 OID 9467368)
-- Name: historico_escolar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6902 (class 2606 OID 9467373)
-- Name: historico_escolar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6901 (class 2606 OID 9467378)
-- Name: historico_grade_curso_id_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_grade_curso_id_fkey FOREIGN KEY (historico_grade_curso_id) REFERENCES pmieducar.historico_grade_curso(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6909 (class 2606 OID 9467383)
-- Name: infra_comodo_funcao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6908 (class 2606 OID 9467388)
-- Name: infra_comodo_funcao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6907 (class 2606 OID 9467393)
-- Name: infra_comodo_ref_cod_escola; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6916 (class 2606 OID 9467398)
-- Name: infra_predio_comodo_ref_cod_infra_comodo_funcao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_cod_infra_comodo_funcao_fkey FOREIGN KEY (ref_cod_infra_comodo_funcao) REFERENCES pmieducar.infra_comodo_funcao(cod_infra_comodo_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6915 (class 2606 OID 9467403)
-- Name: infra_predio_comodo_ref_cod_infra_predio_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_cod_infra_predio_fkey FOREIGN KEY (ref_cod_infra_predio) REFERENCES pmieducar.infra_predio(cod_infra_predio) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6914 (class 2606 OID 9467408)
-- Name: infra_predio_comodo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6913 (class 2606 OID 9467413)
-- Name: infra_predio_comodo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6912 (class 2606 OID 9467418)
-- Name: infra_predio_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6911 (class 2606 OID 9467423)
-- Name: infra_predio_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6910 (class 2606 OID 9467428)
-- Name: infra_predio_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7020 (class 2606 OID 9467433)
-- Name: instituicao_curso_superior_1_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT instituicao_curso_superior_1_fk FOREIGN KEY (instituicao_curso_superior_1) REFERENCES modules.educacenso_ies(id);


--
-- TOC entry 7021 (class 2606 OID 9467438)
-- Name: instituicao_curso_superior_2_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT instituicao_curso_superior_2_fk FOREIGN KEY (instituicao_curso_superior_2) REFERENCES modules.educacenso_ies(id);


--
-- TOC entry 7022 (class 2606 OID 9467443)
-- Name: instituicao_curso_superior_3_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT instituicao_curso_superior_3_fk FOREIGN KEY (instituicao_curso_superior_3) REFERENCES modules.educacenso_ies(id);


--
-- TOC entry 6920 (class 2606 OID 9467448)
-- Name: instituicao_id_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.instituicao_documentacao
    ADD CONSTRAINT instituicao_id_fkey FOREIGN KEY (instituicao_id) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6919 (class 2606 OID 9467453)
-- Name: instituicao_ref_idtlog_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_ref_idtlog_fkey FOREIGN KEY (ref_idtlog) REFERENCES urbano.tipo_logradouro(idtlog) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6918 (class 2606 OID 9467458)
-- Name: instituicao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6917 (class 2606 OID 9467463)
-- Name: instituicao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6924 (class 2606 OID 9467468)
-- Name: material_didatico_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6923 (class 2606 OID 9467473)
-- Name: material_didatico_ref_cod_material_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_cod_material_tipo_fkey FOREIGN KEY (ref_cod_material_tipo) REFERENCES pmieducar.material_tipo(cod_material_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6922 (class 2606 OID 9467478)
-- Name: material_didatico_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6921 (class 2606 OID 9467483)
-- Name: material_didatico_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6927 (class 2606 OID 9467488)
-- Name: material_tipo_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6926 (class 2606 OID 9467493)
-- Name: material_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6925 (class 2606 OID 9467498)
-- Name: material_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6937 (class 2606 OID 9467503)
-- Name: matricula_excessao_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_excessao
    ADD CONSTRAINT matricula_excessao_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6936 (class 2606 OID 9467508)
-- Name: matricula_excessao_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_excessao
    ADD CONSTRAINT matricula_excessao_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula, ref_cod_turma, ref_sequencial) REFERENCES pmieducar.matricula_turma(ref_cod_matricula, ref_cod_turma, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6941 (class 2606 OID 9467513)
-- Name: matricula_ocorrencia_discipli_ref_cod_tipo_ocorrencia_disc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_discipli_ref_cod_tipo_ocorrencia_disc_fkey FOREIGN KEY (ref_cod_tipo_ocorrencia_disciplinar) REFERENCES pmieducar.tipo_ocorrencia_disciplinar(cod_tipo_ocorrencia_disciplinar) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6940 (class 2606 OID 9467518)
-- Name: matricula_ocorrencia_disciplinar_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6939 (class 2606 OID 9467523)
-- Name: matricula_ocorrencia_disciplinar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6938 (class 2606 OID 9467528)
-- Name: matricula_ocorrencia_disciplinar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6934 (class 2606 OID 9467533)
-- Name: matricula_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6933 (class 2606 OID 9467538)
-- Name: matricula_ref_cod_curso; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6932 (class 2606 OID 9467543)
-- Name: matricula_ref_cod_reserva_vaga_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_cod_reserva_vaga_fkey FOREIGN KEY (ref_cod_reserva_vaga) REFERENCES pmieducar.reserva_vaga(cod_reserva_vaga) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6931 (class 2606 OID 9467548)
-- Name: matricula_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6930 (class 2606 OID 9467553)
-- Name: matricula_ref_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_ref_cod_serie_fkey FOREIGN KEY (ref_ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6929 (class 2606 OID 9467558)
-- Name: matricula_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6928 (class 2606 OID 9467563)
-- Name: matricula_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6945 (class 2606 OID 9467568)
-- Name: matricula_turma_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6944 (class 2606 OID 9467573)
-- Name: matricula_turma_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6943 (class 2606 OID 9467578)
-- Name: matricula_turma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6942 (class 2606 OID 9467583)
-- Name: matricula_turma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6947 (class 2606 OID 9468963)
-- Name: menu_tipo_usuario_ref_cod_menu_submenu_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_ref_cod_menu_submenu_fkey FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6946 (class 2606 OID 9468968)
-- Name: menu_tipo_usuario_ref_cod_tipo_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES pmieducar.tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6950 (class 2606 OID 9467588)
-- Name: modulo_ref_cod_instituicao_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6949 (class 2606 OID 9467593)
-- Name: modulo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6948 (class 2606 OID 9467598)
-- Name: modulo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6953 (class 2606 OID 9467603)
-- Name: motivo_afastamento_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6952 (class 2606 OID 9467608)
-- Name: motivo_afastamento_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6951 (class 2606 OID 9467613)
-- Name: motivo_afastamento_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6956 (class 2606 OID 9467618)
-- Name: motivo_baixa_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6955 (class 2606 OID 9467623)
-- Name: motivo_baixa_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6954 (class 2606 OID 9467628)
-- Name: motivo_baixa_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6959 (class 2606 OID 9467633)
-- Name: motivo_suspensao_ref_cod_biblioteca_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_cod_biblioteca_fkey FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6958 (class 2606 OID 9467638)
-- Name: motivo_suspensao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6957 (class 2606 OID 9467643)
-- Name: motivo_suspensao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6966 (class 2606 OID 9467648)
-- Name: nivel_ensino_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6965 (class 2606 OID 9467653)
-- Name: nivel_ensino_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6964 (class 2606 OID 9467658)
-- Name: nivel_ensino_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6963 (class 2606 OID 9467663)
-- Name: nivel_ref_cod_categoria_nivel_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_cod_categoria_nivel_fkey FOREIGN KEY (ref_cod_categoria_nivel) REFERENCES pmieducar.categoria_nivel(cod_categoria_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6962 (class 2606 OID 9467668)
-- Name: nivel_ref_cod_nivel_anterior_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_cod_nivel_anterior_fkey FOREIGN KEY (ref_cod_nivel_anterior) REFERENCES pmieducar.nivel(cod_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6961 (class 2606 OID 9467673)
-- Name: nivel_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6960 (class 2606 OID 9467678)
-- Name: nivel_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6972 (class 2606 OID 9467683)
-- Name: nota_aluno_ref_cod_curso_disciplina; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_curso_disciplina FOREIGN KEY (ref_cod_curso_disciplina) REFERENCES pmieducar.disciplina(cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6971 (class 2606 OID 9467688)
-- Name: nota_aluno_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6970 (class 2606 OID 9467693)
-- Name: nota_aluno_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6969 (class 2606 OID 9467698)
-- Name: nota_aluno_ref_ref_cod_tipo_avaliacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_ref_cod_tipo_avaliacao, ref_sequencial) REFERENCES pmieducar.tipo_avaliacao_valores(ref_cod_tipo_avaliacao, sequencial) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6968 (class 2606 OID 9467703)
-- Name: nota_aluno_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6967 (class 2606 OID 9467708)
-- Name: nota_aluno_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6974 (class 2606 OID 9467713)
-- Name: operador_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.operador
    ADD CONSTRAINT operador_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6973 (class 2606 OID 9467718)
-- Name: operador_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.operador
    ADD CONSTRAINT operador_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6977 (class 2606 OID 9467723)
-- Name: pagamento_divida_ref_cod_biblioteca; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_divida_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6976 (class 2606 OID 9467728)
-- Name: pagamento_multa_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_multa_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6975 (class 2606 OID 9467733)
-- Name: pagamento_multa_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_multa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6770 (class 2606 OID 9467738)
-- Name: pmieducar_bloqueio_ano_letivo_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.bloqueio_ano_letivo
    ADD CONSTRAINT pmieducar_bloqueio_ano_letivo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao);


--
-- TOC entry 6981 (class 2606 OID 9467743)
-- Name: pmieducar_projeto_aluno_ref_cod_aluno; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.projeto_aluno
    ADD CONSTRAINT pmieducar_projeto_aluno_ref_cod_aluno FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno);


--
-- TOC entry 6980 (class 2606 OID 9467748)
-- Name: pmieducar_projeto_aluno_ref_cod_projeto; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.projeto_aluno
    ADD CONSTRAINT pmieducar_projeto_aluno_ref_cod_projeto FOREIGN KEY (ref_cod_projeto) REFERENCES pmieducar.projeto(cod_projeto);


--
-- TOC entry 6979 (class 2606 OID 9467753)
-- Name: pre_requisito_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.pre_requisito
    ADD CONSTRAINT pre_requisito_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6978 (class 2606 OID 9467758)
-- Name: pre_requisito_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.pre_requisito
    ADD CONSTRAINT pre_requisito_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6990 (class 2606 OID 9467763)
-- Name: quadro_horario_horarios_ref_cod_quadro_horario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_quadro_horario_fkey FOREIGN KEY (ref_cod_quadro_horario) REFERENCES pmieducar.quadro_horario(cod_quadro_horario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6987 (class 2606 OID 9467768)
-- Name: quadro_horario_horarios_ref_cod_quadro_horario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_quadro_horario_fkey FOREIGN KEY (ref_cod_quadro_horario) REFERENCES pmieducar.quadro_horario(cod_quadro_horario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6989 (class 2606 OID 9467773)
-- Name: quadro_horario_horarios_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina) REFERENCES pmieducar.escola_serie_disciplina(ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6988 (class 2606 OID 9467778)
-- Name: quadro_horario_horarios_ref_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_fkey FOREIGN KEY (ref_servidor, ref_cod_instituicao_servidor) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6986 (class 2606 OID 9467783)
-- Name: quadro_horario_horarios_ref_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_fkey FOREIGN KEY (ref_servidor, ref_cod_instituicao_servidor) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6985 (class 2606 OID 9467788)
-- Name: quadro_horario_horarios_ref_servidor_substituto_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_ref_servidor_substituto_fkey FOREIGN KEY (ref_servidor_substituto, ref_cod_instituicao_substituto) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6984 (class 2606 OID 9467793)
-- Name: quadro_horario_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6983 (class 2606 OID 9467798)
-- Name: quadro_horario_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6982 (class 2606 OID 9467803)
-- Name: quadro_horario_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6789 (class 2606 OID 9467808)
-- Name: ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno);


--
-- TOC entry 6829 (class 2606 OID 9467813)
-- Name: ref_cod_disciplina; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.dispensa_etapa
    ADD CONSTRAINT ref_cod_disciplina FOREIGN KEY (ref_cod_dispensa) REFERENCES pmieducar.dispensa_disciplina(cod_dispensa);


--
-- TOC entry 6788 (class 2606 OID 9467818)
-- Name: ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES cadastro.pessoa(idpes);


--
-- TOC entry 6787 (class 2606 OID 9467823)
-- Name: ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie);


--
-- TOC entry 6786 (class 2606 OID 9467828)
-- Name: ref_cod_turno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT ref_cod_turno_fkey FOREIGN KEY (ref_cod_turno) REFERENCES pmieducar.turma_turno(id);


--
-- TOC entry 6992 (class 2606 OID 9467833)
-- Name: relacao_categoria_acervo_categoria_id_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.relacao_categoria_acervo
    ADD CONSTRAINT relacao_categoria_acervo_categoria_id_fkey FOREIGN KEY (categoria_id) REFERENCES pmieducar.categoria_obra(id);


--
-- TOC entry 6991 (class 2606 OID 9467838)
-- Name: relacao_categoria_acervo_ref_cod_acervo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.relacao_categoria_acervo
    ADD CONSTRAINT relacao_categoria_acervo_ref_cod_acervo_fkey FOREIGN KEY (ref_cod_acervo) REFERENCES pmieducar.acervo(cod_acervo);


--
-- TOC entry 6994 (class 2606 OID 9467843)
-- Name: religiao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.religiao
    ADD CONSTRAINT religiao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6993 (class 2606 OID 9467848)
-- Name: religiao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.religiao
    ADD CONSTRAINT religiao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6998 (class 2606 OID 9467853)
-- Name: reserva_vaga_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6997 (class 2606 OID 9467858)
-- Name: reserva_vaga_ref_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_ref_cod_serie_fkey FOREIGN KEY (ref_ref_cod_serie, ref_ref_cod_escola) REFERENCES pmieducar.escola_serie(ref_cod_serie, ref_cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6996 (class 2606 OID 9467863)
-- Name: reserva_vaga_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6995 (class 2606 OID 9467868)
-- Name: reserva_vaga_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7001 (class 2606 OID 9467873)
-- Name: reservas_ref_cod_cliente_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_ref_cod_cliente_fkey FOREIGN KEY (ref_cod_cliente) REFERENCES pmieducar.cliente(cod_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7000 (class 2606 OID 9467878)
-- Name: reservas_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6999 (class 2606 OID 9467883)
-- Name: reservas_ref_usuario_libera_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_ref_usuario_libera_fkey FOREIGN KEY (ref_usuario_libera) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7006 (class 2606 OID 9467888)
-- Name: sequencia_serie_ref_serie_destino_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_serie_destino_fkey FOREIGN KEY (ref_serie_destino) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7005 (class 2606 OID 9467893)
-- Name: sequencia_serie_ref_serie_origem_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_serie_origem_fkey FOREIGN KEY (ref_serie_origem) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7004 (class 2606 OID 9467898)
-- Name: sequencia_serie_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7003 (class 2606 OID 9467903)
-- Name: sequencia_serie_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7014 (class 2606 OID 9467908)
-- Name: serie_pre_requisito_ref_cod_operador_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_operador_fkey FOREIGN KEY (ref_cod_operador) REFERENCES pmieducar.operador(cod_operador) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7013 (class 2606 OID 9467913)
-- Name: serie_pre_requisito_ref_cod_pre_requisito_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_pre_requisito_fkey FOREIGN KEY (ref_cod_pre_requisito) REFERENCES pmieducar.pre_requisito(cod_pre_requisito) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7012 (class 2606 OID 9467918)
-- Name: serie_pre_requisito_ref_cod_serie_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7011 (class 2606 OID 9467923)
-- Name: serie_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7010 (class 2606 OID 9467928)
-- Name: serie_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7009 (class 2606 OID 9467933)
-- Name: serie_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7008 (class 2606 OID 9467938)
-- Name: serie_regra_avaliacao_diferenciada_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_regra_avaliacao_diferenciada_fk FOREIGN KEY (regra_avaliacao_diferenciada_id) REFERENCES modules.regra_avaliacao(id) ON DELETE RESTRICT;


--
-- TOC entry 7007 (class 2606 OID 9467943)
-- Name: serie_regra_avaliacao_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_regra_avaliacao_fk FOREIGN KEY (regra_avaliacao_id) REFERENCES modules.regra_avaliacao(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7018 (class 2606 OID 9467948)
-- Name: serie_vaga_ref_cod_curso_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_curso_fk FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7017 (class 2606 OID 9467953)
-- Name: serie_vaga_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7016 (class 2606 OID 9467958)
-- Name: serie_vaga_ref_cod_instituicao_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7015 (class 2606 OID 9467963)
-- Name: serie_vaga_ref_cod_serie_fk; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT serie_vaga_ref_cod_serie_fk FOREIGN KEY (ref_cod_serie) REFERENCES pmieducar.serie(cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7032 (class 2606 OID 9467968)
-- Name: servidor_afastamento_ref_cod_motivo_afastamento_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_cod_motivo_afastamento_fkey FOREIGN KEY (ref_cod_motivo_afastamento) REFERENCES pmieducar.motivo_afastamento(cod_motivo_afastamento) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7031 (class 2606 OID 9467973)
-- Name: servidor_afastamento_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7030 (class 2606 OID 9467978)
-- Name: servidor_afastamento_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7029 (class 2606 OID 9467983)
-- Name: servidor_afastamento_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7036 (class 2606 OID 9467988)
-- Name: servidor_alocacao_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7035 (class 2606 OID 9467993)
-- Name: servidor_alocacao_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7034 (class 2606 OID 9467998)
-- Name: servidor_alocacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7033 (class 2606 OID 9468003)
-- Name: servidor_alocacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7037 (class 2606 OID 9468008)
-- Name: servidor_curso_ref_cod_formacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_curso
    ADD CONSTRAINT servidor_curso_ref_cod_formacao_fkey FOREIGN KEY (ref_cod_formacao) REFERENCES pmieducar.servidor_formacao(cod_formacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7039 (class 2606 OID 9468013)
-- Name: servidor_cuso_ministra_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7038 (class 2606 OID 9468018)
-- Name: servidor_cuso_ministra_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7041 (class 2606 OID 9468023)
-- Name: servidor_disciplina_ref_cod_disciplina_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey FOREIGN KEY (ref_cod_disciplina) REFERENCES modules.componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7040 (class 2606 OID 9468028)
-- Name: servidor_disciplina_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7044 (class 2606 OID 9468033)
-- Name: servidor_formacao_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7043 (class 2606 OID 9468038)
-- Name: servidor_formacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7042 (class 2606 OID 9468043)
-- Name: servidor_formacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7046 (class 2606 OID 9468048)
-- Name: servidor_funcao_ref_cod_funcao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_funcao
    ADD CONSTRAINT servidor_funcao_ref_cod_funcao_fkey FOREIGN KEY (ref_cod_funcao) REFERENCES pmieducar.funcao(cod_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7045 (class 2606 OID 9468053)
-- Name: servidor_funcao_ref_cod_servidor_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_funcao
    ADD CONSTRAINT servidor_funcao_ref_cod_servidor_fkey FOREIGN KEY (ref_cod_servidor, ref_ref_cod_instituicao) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7023 (class 2606 OID 9468058)
-- Name: servidor_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7024 (class 2606 OID 9468063)
-- Name: servidor_ref_cod_subnivel_; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_ref_cod_subnivel_ FOREIGN KEY (ref_cod_subnivel) REFERENCES pmieducar.subnivel(cod_subnivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7025 (class 2606 OID 9468068)
-- Name: servidor_ref_idesco_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_ref_idesco_fkey FOREIGN KEY (ref_idesco) REFERENCES cadastro.escolaridade(idesco) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7047 (class 2606 OID 9468073)
-- Name: servidor_titulo_concurso_ref_cod_formacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.servidor_titulo_concurso
    ADD CONSTRAINT servidor_titulo_concurso_ref_cod_formacao_fkey FOREIGN KEY (ref_cod_formacao) REFERENCES pmieducar.servidor_formacao(cod_formacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7050 (class 2606 OID 9468078)
-- Name: situacao_ref_cod_biblioteca; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_ref_cod_biblioteca FOREIGN KEY (ref_cod_biblioteca) REFERENCES pmieducar.biblioteca(cod_biblioteca) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7049 (class 2606 OID 9468083)
-- Name: situacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7048 (class 2606 OID 9468088)
-- Name: situacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7054 (class 2606 OID 9468093)
-- Name: subnivel_ref_cod_nivel_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_cod_nivel_fkey FOREIGN KEY (ref_cod_nivel) REFERENCES pmieducar.nivel(cod_nivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7053 (class 2606 OID 9468098)
-- Name: subnivel_ref_cod_subnivel_anterior_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_cod_subnivel_anterior_fkey FOREIGN KEY (ref_cod_subnivel_anterior) REFERENCES pmieducar.subnivel(cod_subnivel) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7052 (class 2606 OID 9468103)
-- Name: subnivel_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7051 (class 2606 OID 9468108)
-- Name: subnivel_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7057 (class 2606 OID 9468113)
-- Name: tipo_avaliacao_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7056 (class 2606 OID 9468118)
-- Name: tipo_avaliacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7055 (class 2606 OID 9468123)
-- Name: tipo_avaliacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7058 (class 2606 OID 9468128)
-- Name: tipo_avaliacao_valores_ref_cod_tipo_avaliacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_avaliacao_valores
    ADD CONSTRAINT tipo_avaliacao_valores_ref_cod_tipo_avaliacao_fkey FOREIGN KEY (ref_cod_tipo_avaliacao) REFERENCES pmieducar.tipo_avaliacao(cod_tipo_avaliacao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7061 (class 2606 OID 9468133)
-- Name: tipo_dispensa_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7060 (class 2606 OID 9468138)
-- Name: tipo_dispensa_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7059 (class 2606 OID 9468143)
-- Name: tipo_dispensa_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7064 (class 2606 OID 9468148)
-- Name: tipo_ensino_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7063 (class 2606 OID 9468153)
-- Name: tipo_ensino_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7062 (class 2606 OID 9468158)
-- Name: tipo_ensino_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7067 (class 2606 OID 9468163)
-- Name: tipo_ocorrencia_disciplinar_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7066 (class 2606 OID 9468168)
-- Name: tipo_ocorrencia_disciplinar_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7065 (class 2606 OID 9468173)
-- Name: tipo_ocorrencia_disciplinar_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7070 (class 2606 OID 9468178)
-- Name: tipo_regime_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7069 (class 2606 OID 9468183)
-- Name: tipo_regime_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7068 (class 2606 OID 9468188)
-- Name: tipo_regime_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7072 (class 2606 OID 9468193)
-- Name: tipo_usuario_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_usuario
    ADD CONSTRAINT tipo_usuario_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7071 (class 2606 OID 9468198)
-- Name: tipo_usuario_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.tipo_usuario
    ADD CONSTRAINT tipo_usuario_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7077 (class 2606 OID 9468203)
-- Name: transferencia_solicitacao_ref_cod_matricula_entrada_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_matricula_entrada_fkey FOREIGN KEY (ref_cod_matricula_entrada) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7076 (class 2606 OID 9468208)
-- Name: transferencia_solicitacao_ref_cod_matricula_saida_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_matricula_saida_fkey FOREIGN KEY (ref_cod_matricula_saida) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7075 (class 2606 OID 9468213)
-- Name: transferencia_solicitacao_ref_cod_transferencia_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_cod_transferencia_tipo_fkey FOREIGN KEY (ref_cod_transferencia_tipo) REFERENCES pmieducar.transferencia_tipo(cod_transferencia_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7074 (class 2606 OID 9468218)
-- Name: transferencia_solicitacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7073 (class 2606 OID 9468223)
-- Name: transferencia_solicitacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7080 (class 2606 OID 9468228)
-- Name: transferencia_tipo_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7079 (class 2606 OID 9468233)
-- Name: transferencia_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7078 (class 2606 OID 9468238)
-- Name: transferencia_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7082 (class 2606 OID 9468248)
-- Name: turma_escola_serie_muil; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_escola_serie_muil FOREIGN KEY (ref_ref_cod_serie_mult, ref_ref_cod_escola_mult) REFERENCES pmieducar.escola_serie(ref_cod_serie, ref_cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7093 (class 2606 OID 9468253)
-- Name: turma_modulo_ref_cod_modulo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma_modulo
    ADD CONSTRAINT turma_modulo_ref_cod_modulo_fkey FOREIGN KEY (ref_cod_modulo) REFERENCES pmieducar.modulo(cod_modulo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7092 (class 2606 OID 9468258)
-- Name: turma_modulo_ref_cod_turma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma_modulo
    ADD CONSTRAINT turma_modulo_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma) REFERENCES pmieducar.turma(cod_turma) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7083 (class 2606 OID 9468263)
-- Name: turma_ref_cod_curso; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_curso FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7084 (class 2606 OID 9468268)
-- Name: turma_ref_cod_infra_predio_comodo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_infra_predio_comodo_fkey FOREIGN KEY (ref_cod_infra_predio_comodo) REFERENCES pmieducar.infra_predio_comodo(cod_infra_predio_comodo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7085 (class 2606 OID 9468273)
-- Name: turma_ref_cod_instituicao; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7086 (class 2606 OID 9468278)
-- Name: turma_ref_cod_regente; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_cod_regente FOREIGN KEY (ref_cod_regente, ref_cod_instituicao_regente) REFERENCES pmieducar.servidor(cod_servidor, ref_cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7087 (class 2606 OID 9468283)
-- Name: turma_ref_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_ref_cod_escola_fkey FOREIGN KEY (ref_ref_cod_escola, ref_ref_cod_serie) REFERENCES pmieducar.escola_serie(ref_cod_escola, ref_cod_serie) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7088 (class 2606 OID 9468288)
-- Name: turma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7089 (class 2606 OID 9468293)
-- Name: turma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7095 (class 2606 OID 9468298)
-- Name: turma_tipo_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma_tipo
    ADD CONSTRAINT turma_tipo_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7094 (class 2606 OID 9468303)
-- Name: turma_tipo_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma_tipo
    ADD CONSTRAINT turma_tipo_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7090 (class 2606 OID 9468308)
-- Name: turma_turma_tipo_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_turma_tipo_fkey FOREIGN KEY (ref_cod_turma_tipo) REFERENCES pmieducar.turma_tipo(cod_turma_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7091 (class 2606 OID 9468313)
-- Name: turma_turno_id_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_turno_id_fkey FOREIGN KEY (turma_turno_id) REFERENCES pmieducar.turma_turno(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7100 (class 2606 OID 9468318)
-- Name: usuario_cod_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_cod_usuario_fkey FOREIGN KEY (cod_usuario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7099 (class 2606 OID 9468323)
-- Name: usuario_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7098 (class 2606 OID 9468328)
-- Name: usuario_ref_cod_tipo_usuario_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_cod_tipo_usuario_fkey FOREIGN KEY (ref_cod_tipo_usuario) REFERENCES pmieducar.tipo_usuario(cod_tipo_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7097 (class 2606 OID 9468333)
-- Name: usuario_ref_funcionario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_funcionario_cad_fkey FOREIGN KEY (ref_funcionario_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7096 (class 2606 OID 9468338)
-- Name: usuario_ref_funcionario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_ref_funcionario_exc_fkey FOREIGN KEY (ref_funcionario_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7101 (class 2606 OID 9468343)
-- Name: funcionario_su_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.funcionario_su
    ADD CONSTRAINT funcionario_su_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 7105 (class 2606 OID 9468348)
-- Name: grupomoderador_ref_cod_grupos_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_cod_grupos_fkey FOREIGN KEY (ref_cod_grupos) REFERENCES pmiotopic.grupos(cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7104 (class 2606 OID 9468353)
-- Name: grupomoderador_ref_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7103 (class 2606 OID 9468358)
-- Name: grupomoderador_ref_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7102 (class 2606 OID 9468363)
-- Name: grupomoderador_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7109 (class 2606 OID 9468368)
-- Name: grupopessoa_ref_cod_grupos_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_cod_grupos_fkey FOREIGN KEY (ref_cod_grupos) REFERENCES pmiotopic.grupos(cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7108 (class 2606 OID 9468373)
-- Name: grupopessoa_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7107 (class 2606 OID 9468378)
-- Name: grupopessoa_ref_pessoa_cadatro_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_pessoa_cadatro_fkey FOREIGN KEY (ref_pessoa_cad, ref_grupos_cad) REFERENCES pmiotopic.grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7106 (class 2606 OID 9468383)
-- Name: grupopessoa_ref_pessoa_exclusao_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_ref_pessoa_exclusao_fkey FOREIGN KEY (ref_pessoa_exc, ref_grupos_exc) REFERENCES pmiotopic.grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7111 (class 2606 OID 9468388)
-- Name: grupos_ref_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupos
    ADD CONSTRAINT grupos_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7110 (class 2606 OID 9468393)
-- Name: grupos_ref_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.grupos
    ADD CONSTRAINT grupos_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7114 (class 2606 OID 9468398)
-- Name: notas_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7113 (class 2606 OID 9468403)
-- Name: notas_ref_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_ref_pessoa_cad_fkey FOREIGN KEY (ref_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7112 (class 2606 OID 9468408)
-- Name: notas_ref_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_ref_pessoa_exc_fkey FOREIGN KEY (ref_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7116 (class 2606 OID 9468413)
-- Name: participante_ref_cod_reuniao_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.participante
    ADD CONSTRAINT participante_ref_cod_reuniao_fkey FOREIGN KEY (ref_cod_reuniao) REFERENCES pmiotopic.reuniao(cod_reuniao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7115 (class 2606 OID 9468418)
-- Name: participante_ref_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.participante
    ADD CONSTRAINT participante_ref_ref_idpes_fkey FOREIGN KEY (ref_ref_idpes, ref_ref_cod_grupos) REFERENCES pmiotopic.grupopessoa(ref_idpes, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7117 (class 2606 OID 9468423)
-- Name: reuniao_ref_moderador_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.reuniao
    ADD CONSTRAINT reuniao_ref_moderador_fkey FOREIGN KEY (ref_moderador, ref_grupos_moderador) REFERENCES pmiotopic.grupomoderador(ref_ref_cod_pessoa_fj, ref_cod_grupos) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7119 (class 2606 OID 9468428)
-- Name: topicoreuniao_ref_cod_reuniao_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.topicoreuniao
    ADD CONSTRAINT topicoreuniao_ref_cod_reuniao_fkey FOREIGN KEY (ref_cod_reuniao) REFERENCES pmiotopic.reuniao(cod_reuniao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7118 (class 2606 OID 9468433)
-- Name: topicoreuniao_ref_cod_topico_fkey; Type: FK CONSTRAINT; Schema: pmiotopic; Owner: -
--

ALTER TABLE ONLY pmiotopic.topicoreuniao
    ADD CONSTRAINT topicoreuniao_ref_cod_topico_fkey FOREIGN KEY (ref_cod_topico) REFERENCES pmiotopic.topico(cod_topico) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7124 (class 2606 OID 9468438)
-- Name: agenda_compromisso_ref_cod_agenda_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_ref_cod_agenda_fkey FOREIGN KEY (ref_cod_agenda) REFERENCES portal.agenda(cod_agenda) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7123 (class 2606 OID 9468443)
-- Name: agenda_compromisso_ref_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_ref_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7122 (class 2606 OID 9468448)
-- Name: agenda_ref_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7121 (class 2606 OID 9468453)
-- Name: agenda_ref_ref_cod_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7120 (class 2606 OID 9468458)
-- Name: agenda_ref_ref_cod_pessoa_own_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_ref_ref_cod_pessoa_own_fkey FOREIGN KEY (ref_ref_cod_pessoa_own) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7126 (class 2606 OID 9468463)
-- Name: agenda_responsavel_ref_cod_agenda_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_ref_cod_agenda_fkey FOREIGN KEY (ref_cod_agenda) REFERENCES portal.agenda(cod_agenda) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7125 (class 2606 OID 9468468)
-- Name: agenda_responsavel_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7130 (class 2606 OID 9468473)
-- Name: compras_editais_editais_empresas_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_ibfk_1 FOREIGN KEY (ref_cod_compras_editais_editais) REFERENCES portal.compras_editais_editais(cod_compras_editais_editais) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7129 (class 2606 OID 9468478)
-- Name: compras_editais_editais_empresas_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_ibfk_2 FOREIGN KEY (ref_cod_compras_editais_empresa) REFERENCES portal.compras_editais_empresa(cod_compras_editais_empresa) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7128 (class 2606 OID 9468483)
-- Name: compras_editais_editais_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7127 (class 2606 OID 9468488)
-- Name: compras_editais_editais_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_ibfk_2 FOREIGN KEY (ref_cod_compras_licitacoes) REFERENCES portal.compras_licitacoes(cod_compras_licitacoes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7131 (class 2606 OID 9468493)
-- Name: compras_editais_empresa_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_editais_empresa
    ADD CONSTRAINT compras_editais_empresa_ibfk_1 FOREIGN KEY (ref_sigla_uf) REFERENCES public.uf(sigla_uf) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7132 (class 2606 OID 9468498)
-- Name: compras_funcionarios_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_funcionarios
    ADD CONSTRAINT compras_funcionarios_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7134 (class 2606 OID 9468503)
-- Name: compras_licitacoes_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_ibfk_1 FOREIGN KEY (ref_cod_compras_modalidade) REFERENCES portal.compras_modalidade(cod_compras_modalidade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7133 (class 2606 OID 9468508)
-- Name: compras_licitacoes_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_ibfk_2 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7140 (class 2606 OID 9468513)
-- Name: compras_pregao_execucao_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_1 FOREIGN KEY (ref_equipe3) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7139 (class 2606 OID 9468518)
-- Name: compras_pregao_execucao_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_2 FOREIGN KEY (ref_pregoeiro) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7138 (class 2606 OID 9468523)
-- Name: compras_pregao_execucao_ibfk_3; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_3 FOREIGN KEY (ref_equipe1) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7137 (class 2606 OID 9468528)
-- Name: compras_pregao_execucao_ibfk_4; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_4 FOREIGN KEY (ref_equipe2) REFERENCES portal.compras_funcionarios(ref_ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7136 (class 2606 OID 9468533)
-- Name: compras_pregao_execucao_ibfk_5; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_5 FOREIGN KEY (ref_cod_compras_final_pregao) REFERENCES portal.compras_final_pregao(cod_compras_final_pregao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7135 (class 2606 OID 9468538)
-- Name: compras_pregao_execucao_ibfk_6; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_ibfk_6 FOREIGN KEY (ref_cod_compras_licitacoes) REFERENCES portal.compras_licitacoes(cod_compras_licitacoes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7145 (class 2606 OID 9468543)
-- Name: fk_to_setor_new; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT fk_to_setor_new FOREIGN KEY (ref_cod_setor_new) REFERENCES pmidrh.setor(cod_setor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7142 (class 2606 OID 9468548)
-- Name: foto_portal_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.foto_portal
    ADD CONSTRAINT foto_portal_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7141 (class 2606 OID 9468553)
-- Name: foto_portal_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.foto_portal
    ADD CONSTRAINT foto_portal_ibfk_2 FOREIGN KEY (ref_cod_credito) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7144 (class 2606 OID 9468558)
-- Name: funcionario_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT funcionario_ibfk_1 FOREIGN KEY (ref_cod_pessoa_fj) REFERENCES cadastro.fisica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7143 (class 2606 OID 9468563)
-- Name: funcionario_ibfk_5; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT funcionario_ibfk_5 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7148 (class 2606 OID 9468568)
-- Name: imagem_ref_cod_imagem_tipo_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_ref_cod_imagem_tipo_fkey FOREIGN KEY (ref_cod_imagem_tipo) REFERENCES portal.imagem_tipo(cod_imagem_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7147 (class 2606 OID 9468573)
-- Name: imagem_ref_cod_pessoa_cad_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_ref_cod_pessoa_cad_fkey FOREIGN KEY (ref_cod_pessoa_cad) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7146 (class 2606 OID 9468578)
-- Name: imagem_ref_cod_pessoa_exc_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_ref_cod_pessoa_exc_fkey FOREIGN KEY (ref_cod_pessoa_exc) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7149 (class 2606 OID 9468583)
-- Name: intranet_segur_permissao_negada_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.intranet_segur_permissao_negada
    ADD CONSTRAINT intranet_segur_permissao_negada_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7150 (class 2606 OID 9468588)
-- Name: jor_arquivo_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.jor_arquivo
    ADD CONSTRAINT jor_arquivo_ibfk_1 FOREIGN KEY (ref_cod_jor_edicao) REFERENCES portal.jor_edicao(cod_jor_edicao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7151 (class 2606 OID 9468593)
-- Name: jor_edicao_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.jor_edicao
    ADD CONSTRAINT jor_edicao_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7152 (class 2606 OID 9468598)
-- Name: mailling_email_conteudo_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_email_conteudo
    ADD CONSTRAINT mailling_email_conteudo_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7155 (class 2606 OID 9468603)
-- Name: mailling_fila_envio_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7154 (class 2606 OID 9468608)
-- Name: mailling_fila_envio_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_2 FOREIGN KEY (ref_cod_mailling_email) REFERENCES portal.mailling_email(cod_mailling_email);


--
-- TOC entry 7153 (class 2606 OID 9468613)
-- Name: mailling_fila_envio_ibfk_3; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_ibfk_3 FOREIGN KEY (ref_cod_mailling_email_conteudo) REFERENCES portal.mailling_email_conteudo(cod_mailling_email_conteudo);


--
-- TOC entry 7157 (class 2606 OID 9468618)
-- Name: mailling_grupo_email_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_ibfk_1 FOREIGN KEY (ref_cod_mailling_email) REFERENCES portal.mailling_email(cod_mailling_email) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7156 (class 2606 OID 9468623)
-- Name: mailling_grupo_email_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_ibfk_2 FOREIGN KEY (ref_cod_mailling_grupo) REFERENCES portal.mailling_grupo(cod_mailling_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7160 (class 2606 OID 9468628)
-- Name: mailling_historico_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7159 (class 2606 OID 9468633)
-- Name: mailling_historico_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_2 FOREIGN KEY (ref_cod_mailling_grupo) REFERENCES portal.mailling_grupo(cod_mailling_grupo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7158 (class 2606 OID 9468638)
-- Name: mailling_historico_ibfk_3; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_ibfk_3 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7161 (class 2606 OID 9468973)
-- Name: menu_funcionario_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.menu_funcionario
    ADD CONSTRAINT menu_funcionario_ibfk_1 FOREIGN KEY (ref_cod_menu_submenu) REFERENCES portal.menu_submenu(cod_menu_submenu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7162 (class 2606 OID 9468643)
-- Name: menu_funcionario_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.menu_funcionario
    ADD CONSTRAINT menu_funcionario_ibfk_2 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7164 (class 2606 OID 9468648)
-- Name: menu_submenu_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.menu_submenu
    ADD CONSTRAINT menu_submenu_ibfk_1 FOREIGN KEY (ref_cod_menu_menu) REFERENCES portal.menu_menu(cod_menu_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7165 (class 2606 OID 9468653)
-- Name: not_portal_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.not_portal
    ADD CONSTRAINT not_portal_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7167 (class 2606 OID 9468658)
-- Name: not_portal_tipo_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7166 (class 2606 OID 9468663)
-- Name: not_portal_tipo_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_ibfk_2 FOREIGN KEY (ref_cod_not_tipo) REFERENCES portal.not_tipo(cod_not_tipo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7168 (class 2606 OID 9468668)
-- Name: not_vinc_portal_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.not_vinc_portal
    ADD CONSTRAINT not_vinc_portal_ibfk_1 FOREIGN KEY (ref_cod_not_portal) REFERENCES portal.not_portal(cod_not_portal) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7169 (class 2606 OID 9468673)
-- Name: notificacao_ref_cod_funcionario_fkey; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.notificacao
    ADD CONSTRAINT notificacao_ref_cod_funcionario_fkey FOREIGN KEY (ref_cod_funcionario) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7170 (class 2606 OID 9468678)
-- Name: pessoa_atividade_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.pessoa_atividade
    ADD CONSTRAINT pessoa_atividade_ibfk_1 FOREIGN KEY (ref_cod_ramo_atividade) REFERENCES portal.pessoa_ramo_atividade(cod_ramo_atividade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7172 (class 2606 OID 9468683)
-- Name: pessoa_fj_pessoa_atividade_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_ibfk_1 FOREIGN KEY (ref_cod_pessoa_atividade) REFERENCES portal.pessoa_atividade(cod_pessoa_atividade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7171 (class 2606 OID 9468688)
-- Name: pessoa_fj_pessoa_atividade_ibfk_2; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_ibfk_2 FOREIGN KEY (ref_cod_pessoa_fj) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7173 (class 2606 OID 9468693)
-- Name: portal_concurso_ibfk_1; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.portal_concurso
    ADD CONSTRAINT portal_concurso_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7163 (class 2606 OID 9468698)
-- Name: ref_cod_menu_pai_fk; Type: FK CONSTRAINT; Schema: portal; Owner: -
--

ALTER TABLE ONLY portal.menu_menu
    ADD CONSTRAINT ref_cod_menu_pai_fk FOREIGN KEY (ref_cod_menu_pai) REFERENCES portal.menu_menu(cod_menu_menu) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6568 (class 2606 OID 9468703)
-- Name: bairro_idsetorbai_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT bairro_idsetorbai_fk FOREIGN KEY (idsetorbai) REFERENCES public.setor_bai(idsetorbai);


--
-- TOC entry 7175 (class 2606 OID 9468708)
-- Name: bairro_regiao_ref_cod_regiao_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro_regiao
    ADD CONSTRAINT bairro_regiao_ref_cod_regiao_fkey FOREIGN KEY (ref_cod_regiao) REFERENCES public.regiao(cod_regiao) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7174 (class 2606 OID 9468713)
-- Name: bairro_regiao_ref_idbai_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro_regiao
    ADD CONSTRAINT bairro_regiao_ref_idbai_fkey FOREIGN KEY (ref_idbai) REFERENCES public.bairro(idbai) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 6569 (class 2606 OID 9468718)
-- Name: fk_bairro_distrito; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_distrito FOREIGN KEY (iddis) REFERENCES public.distrito(iddis);


--
-- TOC entry 6570 (class 2606 OID 9468723)
-- Name: fk_bairro_municipio; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);


--
-- TOC entry 6571 (class 2606 OID 9468728)
-- Name: fk_bairro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6572 (class 2606 OID 9468733)
-- Name: fk_bairro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6573 (class 2606 OID 9468738)
-- Name: fk_bairro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6574 (class 2606 OID 9468743)
-- Name: fk_bairro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT fk_bairro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 7180 (class 2606 OID 9468748)
-- Name: fk_distrito_municipio; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);


--
-- TOC entry 7179 (class 2606 OID 9468753)
-- Name: fk_distrito_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 7178 (class 2606 OID 9468758)
-- Name: fk_distrito_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 7177 (class 2606 OID 9468763)
-- Name: fk_distrito_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 7176 (class 2606 OID 9468768)
-- Name: fk_distrito_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT fk_distrito_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 7181 (class 2606 OID 9468773)
-- Name: fk_logr_logr_fonetico; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logradouro_fonetico
    ADD CONSTRAINT fk_logr_logr_fonetico FOREIGN KEY (idlog) REFERENCES public.logradouro(idlog);


--
-- TOC entry 6580 (class 2606 OID 9468778)
-- Name: fk_logradouro_municipio; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_logradouro_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);


--
-- TOC entry 6579 (class 2606 OID 9468783)
-- Name: fk_logradouro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6578 (class 2606 OID 9468788)
-- Name: fk_logradouro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6577 (class 2606 OID 9468793)
-- Name: fk_logradouro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6576 (class 2606 OID 9468798)
-- Name: fk_logradouro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6575 (class 2606 OID 9468803)
-- Name: fk_logradouro_tipo_log; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT fk_logradouro_tipo_log FOREIGN KEY (idtlog) REFERENCES urbano.tipo_logradouro(idtlog);


--
-- TOC entry 6581 (class 2606 OID 9468808)
-- Name: fk_municipio_municipiopai; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_municipiopai FOREIGN KEY (idmun_pai) REFERENCES public.municipio(idmun);


--
-- TOC entry 6582 (class 2606 OID 9468813)
-- Name: fk_municipio_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6583 (class 2606 OID 9468818)
-- Name: fk_municipio_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 6584 (class 2606 OID 9468823)
-- Name: fk_municipio_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6585 (class 2606 OID 9468828)
-- Name: fk_municipio_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 6586 (class 2606 OID 9468833)
-- Name: fk_municipio_uf; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT fk_municipio_uf FOREIGN KEY (sigla_uf) REFERENCES public.uf(sigla_uf);


--
-- TOC entry 7183 (class 2606 OID 9468838)
-- Name: fk_setor_idsetredir; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.setor
    ADD CONSTRAINT fk_setor_idsetredir FOREIGN KEY (idsetredir) REFERENCES public.setor(idset) ON DELETE RESTRICT;


--
-- TOC entry 7182 (class 2606 OID 9468843)
-- Name: fk_setor_idsetsub; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.setor
    ADD CONSTRAINT fk_setor_idsetsub FOREIGN KEY (idsetsub) REFERENCES public.setor(idset) ON DELETE CASCADE;


--
-- TOC entry 7184 (class 2606 OID 9468848)
-- Name: fk_uf_pais; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.uf
    ADD CONSTRAINT fk_uf_pais FOREIGN KEY (idpais) REFERENCES public.pais(idpais);


--
-- TOC entry 7185 (class 2606 OID 9468853)
-- Name: fk_vila_municipio; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vila
    ADD CONSTRAINT fk_vila_municipio FOREIGN KEY (idmun) REFERENCES public.municipio(idmun);


--
-- TOC entry 7186 (class 2606 OID 9468858)
-- Name: aluno_cod_aluno_cod_aluno_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.aluno_cod_aluno
    ADD CONSTRAINT aluno_cod_aluno_cod_aluno_fk FOREIGN KEY (cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7187 (class 2606 OID 9468863)
-- Name: aluno_uniforme_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.aluno_uniforme
    ADD CONSTRAINT aluno_uniforme_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7188 (class 2606 OID 9468868)
-- Name: escola_agua_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_agua
    ADD CONSTRAINT escola_agua_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7189 (class 2606 OID 9468873)
-- Name: escola_energia_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_energia
    ADD CONSTRAINT escola_energia_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7190 (class 2606 OID 9468878)
-- Name: escola_lingua_indigena_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_lingua_indigena
    ADD CONSTRAINT escola_lingua_indigena_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7191 (class 2606 OID 9468883)
-- Name: escola_lixo_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_lixo
    ADD CONSTRAINT escola_lixo_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7192 (class 2606 OID 9468888)
-- Name: escola_projeto_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_projeto
    ADD CONSTRAINT escola_projeto_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7193 (class 2606 OID 9468893)
-- Name: escola_regulamentacao_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_regulamentacao
    ADD CONSTRAINT escola_regulamentacao_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7194 (class 2606 OID 9468898)
-- Name: escola_sanitario_ref_cod_escola_fk; Type: FK CONSTRAINT; Schema: serieciasc; Owner: -
--

ALTER TABLE ONLY serieciasc.escola_sanitario
    ADD CONSTRAINT escola_sanitario_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 7205 (class 2606 OID 9468903)
-- Name: fk_cep_log_bairro_bai; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_log_bairro_bai FOREIGN KEY (idbai) REFERENCES public.bairro(idbai);


--
-- TOC entry 7204 (class 2606 OID 9468908)
-- Name: fk_cep_log_bairro_cep_log; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_log_bairro_cep_log FOREIGN KEY (cep, idlog) REFERENCES urbano.cep_logradouro(cep, idlog) ON DELETE CASCADE;


--
-- TOC entry 7203 (class 2606 OID 9468913)
-- Name: fk_cep_logradouro_bairro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 7202 (class 2606 OID 9468918)
-- Name: fk_cep_logradouro_bairro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 7201 (class 2606 OID 9468923)
-- Name: fk_cep_logradouro_bairro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 7200 (class 2606 OID 9468928)
-- Name: fk_cep_logradouro_bairro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT fk_cep_logradouro_bairro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 7199 (class 2606 OID 9468933)
-- Name: fk_cep_logradouro_logradouro; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_logradouro FOREIGN KEY (idlog) REFERENCES public.logradouro(idlog) ON DELETE CASCADE;


--
-- TOC entry 7198 (class 2606 OID 9468938)
-- Name: fk_cep_logradouro_sistema_idpes_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 7197 (class 2606 OID 9468943)
-- Name: fk_cep_logradouro_sistema_idpes_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;


--
-- TOC entry 7196 (class 2606 OID 9468948)
-- Name: fk_cep_logradouro_sistema_idsis_cad; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;


--
-- TOC entry 7195 (class 2606 OID 9468953)
-- Name: fk_cep_logradouro_sistema_idsis_rev; Type: FK CONSTRAINT; Schema: urbano; Owner: -
--

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT fk_cep_logradouro_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;
