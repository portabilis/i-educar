ALTER TABLE ONLY acesso.funcao
    ADD CONSTRAINT pk_funcao PRIMARY KEY (idfunc, idsis, idmen);

ALTER TABLE ONLY acesso.grupo
    ADD CONSTRAINT pk_grupo PRIMARY KEY (idgrp);

ALTER TABLE ONLY acesso.grupo_funcao
    ADD CONSTRAINT pk_grupo_funcao PRIMARY KEY (idmen, idsis, idgrp, idfunc);

ALTER TABLE ONLY acesso.grupo_menu
    ADD CONSTRAINT pk_grupo_menu PRIMARY KEY (idgrp, idsis, idmen);

ALTER TABLE ONLY acesso.grupo_operacao
    ADD CONSTRAINT pk_grupo_operacao PRIMARY KEY (idfunc, idgrp, idsis, idmen, idope);

ALTER TABLE ONLY acesso.grupo_sistema
    ADD CONSTRAINT pk_grupo_sistema PRIMARY KEY (idsis, idgrp);

ALTER TABLE ONLY acesso.historico_senha
    ADD CONSTRAINT pk_historico_senha PRIMARY KEY (login, senha);

ALTER TABLE ONLY acesso.instituicao
    ADD CONSTRAINT pk_instituicao PRIMARY KEY (idins);

ALTER TABLE ONLY acesso.menu
    ADD CONSTRAINT pk_menu PRIMARY KEY (idsis, idmen);

ALTER TABLE ONLY acesso.operacao
    ADD CONSTRAINT pk_operacao PRIMARY KEY (idope);

ALTER TABLE ONLY acesso.operacao_funcao
    ADD CONSTRAINT pk_operacao_funcao PRIMARY KEY (idmen, idsis, idfunc, idope);

ALTER TABLE ONLY acesso.pessoa_instituicao
    ADD CONSTRAINT pk_pessoa_instituicao PRIMARY KEY (idins, idpes);

ALTER TABLE ONLY acesso.sistema
    ADD CONSTRAINT pk_sistema PRIMARY KEY (idsis);

ALTER TABLE ONLY acesso.usuario
    ADD CONSTRAINT pk_usuario PRIMARY KEY (login);

ALTER TABLE ONLY acesso.usuario_grupo
    ADD CONSTRAINT pk_usuario_grupo PRIMARY KEY (idgrp, login);

ALTER TABLE ONLY alimentos.baixa_guia_produto
    ADD CONSTRAINT pk_baixa_guia_produto PRIMARY KEY (idbap);

ALTER TABLE ONLY alimentos.baixa_guia_remessa
    ADD CONSTRAINT pk_baixa_guia_remessa PRIMARY KEY (idbai);

ALTER TABLE ONLY alimentos.calendario
    ADD CONSTRAINT pk_calendario PRIMARY KEY (idcad);

ALTER TABLE ONLY alimentos.cardapio
    ADD CONSTRAINT pk_cardapio PRIMARY KEY (idcar);

ALTER TABLE ONLY alimentos.cardapio_faixa_unidade
    ADD CONSTRAINT pk_cardapio_faixa_unidade PRIMARY KEY (idfeu, idcar);

ALTER TABLE ONLY alimentos.cardapio_produto
    ADD CONSTRAINT pk_cardapio_produto PRIMARY KEY (idcpr);

ALTER TABLE ONLY alimentos.cardapio_receita
    ADD CONSTRAINT pk_cardapio_receita PRIMARY KEY (idcar, idrec);

ALTER TABLE ONLY alimentos.cliente
    ADD CONSTRAINT pk_cliente PRIMARY KEY (idcli);

ALTER TABLE ONLY alimentos.contrato
    ADD CONSTRAINT pk_contrato PRIMARY KEY (idcon);

ALTER TABLE ONLY alimentos.contrato_produto
    ADD CONSTRAINT pk_contrato_produto PRIMARY KEY (idcop);

ALTER TABLE ONLY alimentos.composto_quimico
    ADD CONSTRAINT pk_cp_quimico PRIMARY KEY (idcom);

ALTER TABLE ONLY alimentos.evento
    ADD CONSTRAINT pk_evento PRIMARY KEY (ideve);

ALTER TABLE ONLY alimentos.faixa_composto_quimico
    ADD CONSTRAINT pk_faixa_composto_quimico PRIMARY KEY (idfcp);

ALTER TABLE ONLY alimentos.faixa_etaria
    ADD CONSTRAINT pk_faixa_etaria PRIMARY KEY (idfae);

ALTER TABLE ONLY alimentos.fornecedor
    ADD CONSTRAINT pk_fornecedor PRIMARY KEY (idfor);

ALTER TABLE ONLY alimentos.grupo_quimico
    ADD CONSTRAINT pk_grp_quimico PRIMARY KEY (idgrpq);

ALTER TABLE ONLY alimentos.guia_produto_diario
    ADD CONSTRAINT pk_guia_produto_diario PRIMARY KEY (idguiaprodiario);

ALTER TABLE ONLY alimentos.guia_remessa
    ADD CONSTRAINT pk_guia_remessa PRIMARY KEY (idgui);

ALTER TABLE ONLY alimentos.guia_remessa_produto
    ADD CONSTRAINT pk_guia_remessa_produto PRIMARY KEY (idgup);

ALTER TABLE ONLY alimentos.log_guia_remessa
    ADD CONSTRAINT pk_log_guia_remessa PRIMARY KEY (idlogguia);

ALTER TABLE ONLY alimentos.medidas_caseiras
    ADD CONSTRAINT pk_medidas_caseiras PRIMARY KEY (idmedcas, idcli);

ALTER TABLE ONLY alimentos.pessoa
    ADD CONSTRAINT pk_pessoa PRIMARY KEY (idpes);

ALTER TABLE ONLY alimentos.produto_composto_quimico
    ADD CONSTRAINT pk_prod_cp_quimico PRIMARY KEY (idpcq);

ALTER TABLE ONLY alimentos.produto
    ADD CONSTRAINT pk_produto PRIMARY KEY (idpro);

ALTER TABLE ONLY alimentos.produto_fornecedor
    ADD CONSTRAINT pk_produto_fornecedor PRIMARY KEY (idprf);

ALTER TABLE ONLY alimentos.produto_medida_caseira
    ADD CONSTRAINT pk_produto_medida_caseira PRIMARY KEY (idpmc);

ALTER TABLE ONLY alimentos.receita_composto_quimico
    ADD CONSTRAINT pk_rec_cp_quimico PRIMARY KEY (idrcq);

ALTER TABLE ONLY alimentos.receita_produto
    ADD CONSTRAINT pk_rec_prod PRIMARY KEY (idrpr);

ALTER TABLE ONLY alimentos.receita
    ADD CONSTRAINT pk_receita PRIMARY KEY (idrec);

ALTER TABLE ONLY alimentos.tipo_unidade
    ADD CONSTRAINT pk_tipo_unidade PRIMARY KEY (idtip);

ALTER TABLE ONLY alimentos.tipo_produto
    ADD CONSTRAINT pk_tp_produto PRIMARY KEY (idtip);

ALTER TABLE ONLY alimentos.tipo_refeicao
    ADD CONSTRAINT pk_tp_refeicao PRIMARY KEY (idtre);

ALTER TABLE ONLY alimentos.unidade_faixa_etaria
    ADD CONSTRAINT pk_uni_faixa_etaria PRIMARY KEY (idfeu);

ALTER TABLE ONLY alimentos.unidade_produto
    ADD CONSTRAINT pk_uni_produto PRIMARY KEY (idunp, idcli);

ALTER TABLE ONLY alimentos.unidade_atendida
    ADD CONSTRAINT pk_unidade_atendida PRIMARY KEY (iduni);

ALTER TABLE ONLY cadastro.fisica_foto
    ADD CONSTRAINT fisica_foto_pkey PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.fisica_sangue
    ADD CONSTRAINT fisica_sangue_pkey PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.aviso_nome
    ADD CONSTRAINT pk_aviso_nome PRIMARY KEY (idpes, aviso);

ALTER TABLE ONLY cadastro.deficiencia
    ADD CONSTRAINT pk_cadastro_escolaridade PRIMARY KEY (cod_deficiencia);

ALTER TABLE ONLY cadastro.documento
    ADD CONSTRAINT pk_documento PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.endereco_externo
    ADD CONSTRAINT pk_endereco_externo PRIMARY KEY (idpes, tipo);

ALTER TABLE ONLY cadastro.endereco_pessoa
    ADD CONSTRAINT pk_endereco_pessoa PRIMARY KEY (idpes, tipo);

ALTER TABLE ONLY cadastro.escolaridade
    ADD CONSTRAINT pk_escolaridade PRIMARY KEY (idesco);

ALTER TABLE ONLY cadastro.estado_civil
    ADD CONSTRAINT pk_estado_civil PRIMARY KEY (ideciv);

ALTER TABLE ONLY cadastro.fisica
    ADD CONSTRAINT pk_fisica PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.fisica_cpf
    ADD CONSTRAINT pk_fisica_cpf PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.fisica_deficiencia
    ADD CONSTRAINT pk_fisica_deficiencia PRIMARY KEY (ref_idpes, ref_cod_deficiencia);

ALTER TABLE ONLY cadastro.fisica_raca
    ADD CONSTRAINT pk_fisica_raca PRIMARY KEY (ref_idpes);

ALTER TABLE ONLY cadastro.fone_pessoa
    ADD CONSTRAINT pk_fone_pessoa PRIMARY KEY (idpes, tipo);

ALTER TABLE ONLY cadastro.funcionario
    ADD CONSTRAINT pk_funcionario PRIMARY KEY (matricula, idins);

ALTER TABLE ONLY cadastro.codigo_cartorio_inep
    ADD CONSTRAINT pk_id PRIMARY KEY (id);

ALTER TABLE ONLY cadastro.juridica
    ADD CONSTRAINT pk_juridica PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.ocupacao
    ADD CONSTRAINT pk_ocupacao PRIMARY KEY (idocup);

ALTER TABLE ONLY cadastro.orgao_emissor_rg
    ADD CONSTRAINT pk_orgao_emissor_rg PRIMARY KEY (idorg_rg);

ALTER TABLE ONLY cadastro.pessoa
    ADD CONSTRAINT pk_pessoa PRIMARY KEY (idpes);

ALTER TABLE ONLY cadastro.pessoa_fonetico
    ADD CONSTRAINT pk_pessoa_fonetico PRIMARY KEY (fonema, idpes);

ALTER TABLE ONLY cadastro.socio
    ADD CONSTRAINT pk_socio PRIMARY KEY (idpes_juridica, idpes_fisica);

ALTER TABLE ONLY cadastro.raca
    ADD CONSTRAINT raca_pkey PRIMARY KEY (cod_raca);

ALTER TABLE ONLY cadastro.religiao
    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);

ALTER TABLE ONLY consistenciacao.campo_consistenciacao
    ADD CONSTRAINT pk_campo_consistenciacao PRIMARY KEY (idcam);

ALTER TABLE ONLY consistenciacao.campo_metadado
    ADD CONSTRAINT pk_campo_metadado PRIMARY KEY (id_campo_met);

ALTER TABLE ONLY consistenciacao.confrontacao
    ADD CONSTRAINT pk_confrontacao PRIMARY KEY (idcon);

ALTER TABLE ONLY consistenciacao.fonte
    ADD CONSTRAINT pk_fonte PRIMARY KEY (idfon);

ALTER TABLE ONLY consistenciacao.historico_campo
    ADD CONSTRAINT pk_historico_campo PRIMARY KEY (idpes, idcam);

ALTER TABLE ONLY consistenciacao.incoerencia_pessoa_possivel
    ADD CONSTRAINT pk_inc_pessoa_possivel PRIMARY KEY (idinc, idpes);

ALTER TABLE ONLY consistenciacao.incoerencia
    ADD CONSTRAINT pk_incoerencia PRIMARY KEY (idinc);

ALTER TABLE ONLY consistenciacao.incoerencia_documento
    ADD CONSTRAINT pk_incoerencia_documento PRIMARY KEY (id_inc_doc);

ALTER TABLE ONLY consistenciacao.incoerencia_endereco
    ADD CONSTRAINT pk_incoerencia_endereco PRIMARY KEY (id_inc_end);

ALTER TABLE ONLY consistenciacao.incoerencia_fone
    ADD CONSTRAINT pk_incoerencia_fone PRIMARY KEY (id_inc_fone);

ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
    ADD CONSTRAINT pk_incoerencia_tipo_incoerencia PRIMARY KEY (id_tipo_inc, idinc);

ALTER TABLE ONLY consistenciacao.metadado
    ADD CONSTRAINT pk_metadado PRIMARY KEY (idmet);

ALTER TABLE ONLY consistenciacao.ocorrencia_regra_campo
    ADD CONSTRAINT pk_ocorrencia_regra_campo PRIMARY KEY (idreg, conteudo_padrao);

ALTER TABLE ONLY consistenciacao.regra_campo
    ADD CONSTRAINT pk_regra_campo PRIMARY KEY (idreg);

ALTER TABLE ONLY consistenciacao.temp_cadastro_unificacao_cmf
    ADD CONSTRAINT pk_temp_cadastro_unificacao_cmf PRIMARY KEY (idpes);

ALTER TABLE ONLY consistenciacao.temp_cadastro_unificacao_siam
    ADD CONSTRAINT pk_temp_cadastro_unificacao_siam PRIMARY KEY (idpes);

ALTER TABLE ONLY consistenciacao.tipo_incoerencia
    ADD CONSTRAINT pk_tipo_incoerencia PRIMARY KEY (id_tipo_inc);

ALTER TABLE ONLY modules.area_conhecimento
    ADD CONSTRAINT area_conhecimento_pkey PRIMARY KEY (id, instituicao_id);

ALTER TABLE ONLY modules.auditoria_geral
    ADD CONSTRAINT auditoria_geral_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.calendario_turma
    ADD CONSTRAINT calendario_turma_pk PRIMARY KEY (calendario_ano_letivo_id, ano, mes, dia, turma_id);

ALTER TABLE ONLY modules.config_movimento_geral
    ADD CONSTRAINT cod_config_movimento_geral_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.componente_curricular_ano_escolar
    ADD CONSTRAINT componente_curricular_ano_escolar_pkey PRIMARY KEY (componente_curricular_id, ano_escolar_id);

ALTER TABLE ONLY modules.componente_curricular
    ADD CONSTRAINT componente_curricular_pkey PRIMARY KEY (id, instituicao_id);

ALTER TABLE ONLY modules.componente_curricular_turma
    ADD CONSTRAINT componente_curricular_turma_pkey PRIMARY KEY (componente_curricular_id, turma_id);

ALTER TABLE ONLY modules.docente_licenciatura
    ADD CONSTRAINT docente_licenciatura_curso_unique UNIQUE (servidor_id, curso_id, ies_id);

ALTER TABLE ONLY modules.docente_licenciatura
    ADD CONSTRAINT docente_licenciatura_pk PRIMARY KEY (id);

ALTER TABLE ONLY modules.educacenso_cod_aluno
    ADD CONSTRAINT educacenso_cod_aluno_pk PRIMARY KEY (cod_aluno, cod_aluno_inep);

ALTER TABLE ONLY modules.educacenso_cod_docente
    ADD CONSTRAINT educacenso_cod_docente_pk PRIMARY KEY (cod_servidor, cod_docente_inep);

ALTER TABLE ONLY modules.educacenso_cod_escola
    ADD CONSTRAINT educacenso_cod_escola_pk PRIMARY KEY (cod_escola, cod_escola_inep);

ALTER TABLE ONLY modules.educacenso_cod_turma
    ADD CONSTRAINT educacenso_cod_turma_pk PRIMARY KEY (cod_turma, cod_turma_inep);

ALTER TABLE ONLY modules.educacenso_curso_superior
    ADD CONSTRAINT educacenso_curso_superior_pk PRIMARY KEY (id);

ALTER TABLE ONLY modules.educacenso_ies
    ADD CONSTRAINT educacenso_ies_pk PRIMARY KEY (id);

ALTER TABLE ONLY modules.empresa_transporte_escolar
    ADD CONSTRAINT empresa_transporte_escolar_cod_empresa_transporte_escolar_pkey PRIMARY KEY (cod_empresa_transporte_escolar);

ALTER TABLE ONLY modules.etapas_curso_educacenso
    ADD CONSTRAINT etapas_curso_educacenso_pk PRIMARY KEY (etapa_id, curso_id);

ALTER TABLE ONLY modules.etapas_educacenso
    ADD CONSTRAINT etapas_educacenso_pk PRIMARY KEY (id);

ALTER TABLE ONLY modules.falta_aluno
    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.falta_componente_curricular
    ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (falta_aluno_id, componente_curricular_id, etapa);

ALTER TABLE ONLY modules.falta_geral
    ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (falta_aluno_id, etapa);

ALTER TABLE ONLY modules.ficha_medica_aluno
    ADD CONSTRAINT ficha_medica_cod_aluno_pkey PRIMARY KEY (ref_cod_aluno);

ALTER TABLE ONLY modules.formula_media
    ADD CONSTRAINT formula_media_pkey PRIMARY KEY (id, instituicao_id);

ALTER TABLE ONLY modules.itinerario_transporte_escolar
    ADD CONSTRAINT itinerario_transporte_escolar_cod_itinerario_transporte_escolar PRIMARY KEY (cod_itinerario_transporte_escolar);

ALTER TABLE ONLY modules.lingua_indigena_educacenso
    ADD CONSTRAINT lingua_indigena_educacenso_pk PRIMARY KEY (id);

ALTER TABLE ONLY modules.media_geral
    ADD CONSTRAINT media_geral_pkey PRIMARY KEY (nota_aluno_id, etapa);

ALTER TABLE ONLY modules.moradia_aluno
    ADD CONSTRAINT moradia_aluno_pkei PRIMARY KEY (ref_cod_aluno);

ALTER TABLE ONLY modules.motorista
    ADD CONSTRAINT motorista_pkey PRIMARY KEY (cod_motorista);

ALTER TABLE ONLY modules.nota_aluno
    ADD CONSTRAINT nota_aluno_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.nota_componente_curricular_media
    ADD CONSTRAINT nota_componente_curricular_media_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id);

ALTER TABLE ONLY modules.nota_componente_curricular
    ADD CONSTRAINT nota_componente_curricular_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id, etapa);

ALTER TABLE ONLY modules.nota_geral
    ADD CONSTRAINT nota_geral_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.parecer_aluno
    ADD CONSTRAINT parecer_aluno_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.parecer_componente_curricular
    ADD CONSTRAINT parecer_componente_curricular_pkey PRIMARY KEY (parecer_aluno_id, componente_curricular_id, etapa);

ALTER TABLE ONLY modules.parecer_geral
    ADD CONSTRAINT parecer_geral_pkey PRIMARY KEY (parecer_aluno_id, etapa);

ALTER TABLE ONLY modules.pessoa_transporte
    ADD CONSTRAINT pessoa_transporte_cod_pessoa_transporte_pkey PRIMARY KEY (cod_pessoa_transporte);

ALTER TABLE ONLY modules.educacenso_orgao_regional
    ADD CONSTRAINT pk_educacenso_orgao_regional PRIMARY KEY (sigla_uf, codigo);

ALTER TABLE ONLY modules.ponto_transporte_escolar
    ADD CONSTRAINT ponto_transporte_escolar_cod_ponto_transporte_escolar_pkey PRIMARY KEY (cod_ponto_transporte_escolar);

ALTER TABLE ONLY modules.professor_turma_disciplina
    ADD CONSTRAINT professor_turma_disciplina_pk PRIMARY KEY (professor_turma_id, componente_curricular_id);

ALTER TABLE ONLY modules.professor_turma
    ADD CONSTRAINT professor_turma_id_pk PRIMARY KEY (id);

ALTER TABLE ONLY modules.regra_avaliacao
    ADD CONSTRAINT regra_avaliacao_pkey PRIMARY KEY (id, instituicao_id);

ALTER TABLE ONLY modules.regra_avaliacao_recuperacao
    ADD CONSTRAINT regra_avaliacao_recuperacao_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.regra_avaliacao_serie_ano
    ADD CONSTRAINT regra_avaliacao_serie_ano_pkey PRIMARY KEY (serie_id, ano_letivo);

ALTER TABLE ONLY modules.rota_transporte_escolar
    ADD CONSTRAINT rota_transporte_escolar_cod_rota_transporte_escolar_pkey PRIMARY KEY (cod_rota_transporte_escolar);

ALTER TABLE ONLY modules.tabela_arredondamento
    ADD CONSTRAINT tabela_arredondamento_pkey PRIMARY KEY (id, instituicao_id);

ALTER TABLE ONLY modules.tabela_arredondamento_valor
    ADD CONSTRAINT tabela_arredondamento_valor_pkey PRIMARY KEY (id);

ALTER TABLE ONLY modules.tipo_veiculo
    ADD CONSTRAINT tipo_veiculo_pkey PRIMARY KEY (cod_tipo_veiculo);

ALTER TABLE ONLY modules.transporte_aluno
    ADD CONSTRAINT transporte_aluno_pk PRIMARY KEY (aluno_id);

ALTER TABLE ONLY modules.uniforme_aluno
    ADD CONSTRAINT uniforme_aluno_pkey PRIMARY KEY (ref_cod_aluno);

ALTER TABLE ONLY modules.veiculo
    ADD CONSTRAINT veiculo_pkey PRIMARY KEY (cod_veiculo);

ALTER TABLE ONLY pmiacoes.acao_governo_arquivo
    ADD CONSTRAINT acao_governo_arquivo_pkey PRIMARY KEY (cod_acao_governo_arquivo);

ALTER TABLE ONLY pmiacoes.acao_governo_categoria
    ADD CONSTRAINT acao_governo_categoria_pkey PRIMARY KEY (ref_cod_categoria, ref_cod_acao_governo);

ALTER TABLE ONLY pmiacoes.acao_governo_foto
    ADD CONSTRAINT acao_governo_foto_pkey PRIMARY KEY (cod_acao_governo_foto);

ALTER TABLE ONLY pmiacoes.acao_governo_foto_portal
    ADD CONSTRAINT acao_governo_foto_portal_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_foto_portal);

ALTER TABLE ONLY pmiacoes.acao_governo_noticia
    ADD CONSTRAINT acao_governo_noticia_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_not_portal);

ALTER TABLE ONLY pmiacoes.acao_governo
    ADD CONSTRAINT acao_governo_pkey PRIMARY KEY (cod_acao_governo);

ALTER TABLE ONLY pmiacoes.acao_governo_setor
    ADD CONSTRAINT acao_governo_setor_pkey PRIMARY KEY (ref_cod_acao_governo, ref_cod_setor);

ALTER TABLE ONLY pmiacoes.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (cod_categoria);

ALTER TABLE ONLY pmiacoes.secretaria_responsavel
    ADD CONSTRAINT secretaria_responsavel_pkey PRIMARY KEY (ref_cod_setor);

ALTER TABLE ONLY pmicontrolesis.acontecimento
    ADD CONSTRAINT acontecimento_pkey PRIMARY KEY (cod_acontecimento);

ALTER TABLE ONLY pmicontrolesis.artigo
    ADD CONSTRAINT artigo_pkey PRIMARY KEY (cod_artigo);

ALTER TABLE ONLY pmicontrolesis.foto_evento
    ADD CONSTRAINT foto_evento_pk PRIMARY KEY (cod_foto_evento);

ALTER TABLE ONLY pmicontrolesis.foto_vinc
    ADD CONSTRAINT foto_vinc_pkey PRIMARY KEY (cod_foto_vinc);

ALTER TABLE ONLY pmicontrolesis.itinerario
    ADD CONSTRAINT itinerario_pkey PRIMARY KEY (cod_itinerario);

ALTER TABLE ONLY pmicontrolesis.menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (cod_menu);

ALTER TABLE ONLY pmicontrolesis.menu_portal
    ADD CONSTRAINT menu_portal_pkey PRIMARY KEY (cod_menu_portal);

ALTER TABLE ONLY pmicontrolesis.portais
    ADD CONSTRAINT portais_pkey PRIMARY KEY (cod_portais);

ALTER TABLE ONLY pmicontrolesis.servicos
    ADD CONSTRAINT servicos_pkey PRIMARY KEY (cod_servicos);

ALTER TABLE ONLY pmicontrolesis.sistema
    ADD CONSTRAINT sistema_pkey PRIMARY KEY (cod_sistema);

ALTER TABLE ONLY pmicontrolesis.submenu_portal
    ADD CONSTRAINT submenu_portal_pkey PRIMARY KEY (cod_submenu_portal);

ALTER TABLE ONLY pmicontrolesis.telefones
    ADD CONSTRAINT telefones_pkey PRIMARY KEY (cod_telefones);

ALTER TABLE ONLY pmicontrolesis.tipo_acontecimento
    ADD CONSTRAINT tipo_acontecimento_pkey PRIMARY KEY (cod_tipo_acontecimento);

ALTER TABLE ONLY pmicontrolesis.topo_portal
    ADD CONSTRAINT topo_portal_pkey PRIMARY KEY (cod_topo_portal);

ALTER TABLE ONLY pmicontrolesis.tutormenu
    ADD CONSTRAINT tutormenu_pkey PRIMARY KEY (cod_tutormenu);

ALTER TABLE ONLY pmidrh.diaria_grupo
    ADD CONSTRAINT diaria_grupo_pkey PRIMARY KEY (cod_diaria_grupo);

ALTER TABLE ONLY pmidrh.diaria
    ADD CONSTRAINT diaria_pkey PRIMARY KEY (cod_diaria);

ALTER TABLE ONLY pmidrh.diaria_valores
    ADD CONSTRAINT diaria_valores_pkey PRIMARY KEY (cod_diaria_valores);

ALTER TABLE ONLY pmidrh.setor
    ADD CONSTRAINT setor_pkey PRIMARY KEY (cod_setor);

ALTER TABLE ONLY pmieducar.acervo_acervo_assunto
    ADD CONSTRAINT acervo_acervo_assunto_pkey PRIMARY KEY (ref_cod_acervo, ref_cod_acervo_assunto);

ALTER TABLE ONLY pmieducar.acervo_acervo_autor
    ADD CONSTRAINT acervo_acervo_autor_pkey PRIMARY KEY (ref_cod_acervo_autor, ref_cod_acervo);

ALTER TABLE ONLY pmieducar.acervo_assunto
    ADD CONSTRAINT acervo_assunto_pkey PRIMARY KEY (cod_acervo_assunto);

ALTER TABLE ONLY pmieducar.acervo_autor
    ADD CONSTRAINT acervo_autor_pkey PRIMARY KEY (cod_acervo_autor);

ALTER TABLE ONLY pmieducar.acervo_colecao
    ADD CONSTRAINT acervo_colecao_pkey PRIMARY KEY (cod_acervo_colecao);

ALTER TABLE ONLY pmieducar.acervo_editora
    ADD CONSTRAINT acervo_editora_pkey PRIMARY KEY (cod_acervo_editora);

ALTER TABLE ONLY pmieducar.acervo_idioma
    ADD CONSTRAINT acervo_idioma_pkey PRIMARY KEY (cod_acervo_idioma);

ALTER TABLE ONLY pmieducar.acervo
    ADD CONSTRAINT acervo_pkey PRIMARY KEY (cod_acervo);

ALTER TABLE ONLY pmieducar.aluno_aluno_beneficio
    ADD CONSTRAINT aluno_aluno_beneficio_pk PRIMARY KEY (aluno_id, aluno_beneficio_id);

ALTER TABLE ONLY pmieducar.aluno_beneficio
    ADD CONSTRAINT aluno_beneficio_pkey PRIMARY KEY (cod_aluno_beneficio);

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_pkey PRIMARY KEY (cod_aluno);

ALTER TABLE ONLY pmieducar.aluno
    ADD CONSTRAINT aluno_ref_idpes_un UNIQUE (ref_idpes);

ALTER TABLE ONLY pmieducar.ano_letivo_modulo
    ADD CONSTRAINT ano_letivo_modulo_pkey PRIMARY KEY (ref_ano, ref_ref_cod_escola, sequencial, ref_cod_modulo);

ALTER TABLE ONLY pmieducar.auditoria_falta_componente_dispensa
    ADD CONSTRAINT auditoria_falta_componente_dispensa_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.auditoria_nota_dispensa
    ADD CONSTRAINT auditoria_nota_dispensa_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.avaliacao_desempenho
    ADD CONSTRAINT avaliacao_desempenho_pkey PRIMARY KEY (sequencial, ref_cod_servidor, ref_ref_cod_instituicao);

ALTER TABLE ONLY pmieducar.backup
    ADD CONSTRAINT backup_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.biblioteca_dia
    ADD CONSTRAINT biblioteca_dia_pkey PRIMARY KEY (ref_cod_biblioteca, dia);

ALTER TABLE ONLY pmieducar.biblioteca_feriados
    ADD CONSTRAINT biblioteca_feriados_pkey PRIMARY KEY (cod_feriado);

ALTER TABLE ONLY pmieducar.biblioteca
    ADD CONSTRAINT biblioteca_pkey PRIMARY KEY (cod_biblioteca);

ALTER TABLE ONLY pmieducar.biblioteca_usuario
    ADD CONSTRAINT biblioteca_usuario_pkey PRIMARY KEY (ref_cod_biblioteca, ref_cod_usuario);

ALTER TABLE ONLY pmieducar.calendario_ano_letivo
    ADD CONSTRAINT calendario_ano_letivo_pkey PRIMARY KEY (cod_calendario_ano_letivo);

ALTER TABLE ONLY pmieducar.calendario_anotacao
    ADD CONSTRAINT calendario_anotacao_pkey PRIMARY KEY (cod_calendario_anotacao);

ALTER TABLE ONLY pmieducar.calendario_dia_anotacao
    ADD CONSTRAINT calendario_dia_anotacao_pkey PRIMARY KEY (ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao);

ALTER TABLE ONLY pmieducar.calendario_dia_motivo
    ADD CONSTRAINT calendario_dia_motivo_pkey PRIMARY KEY (cod_calendario_dia_motivo);

ALTER TABLE ONLY pmieducar.calendario_dia
    ADD CONSTRAINT calendario_dia_pkey PRIMARY KEY (ref_cod_calendario_ano_letivo, mes, dia);

ALTER TABLE ONLY pmieducar.categoria_nivel
    ADD CONSTRAINT categoria_nivel_pkey PRIMARY KEY (cod_categoria_nivel);

ALTER TABLE ONLY pmieducar.categoria_obra
    ADD CONSTRAINT categoria_obra_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_login_ukey UNIQUE (login);

ALTER TABLE ONLY pmieducar.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (cod_cliente);

ALTER TABLE ONLY pmieducar.cliente_suspensao
    ADD CONSTRAINT cliente_suspensao_pkey PRIMARY KEY (sequencial, ref_cod_cliente, ref_cod_motivo_suspensao);

ALTER TABLE ONLY pmieducar.cliente_tipo_cliente
    ADD CONSTRAINT cliente_tipo_cliente_pk PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_cliente);

ALTER TABLE ONLY pmieducar.cliente_tipo_exemplar_tipo
    ADD CONSTRAINT cliente_tipo_exemplar_tipo_pkey PRIMARY KEY (ref_cod_cliente_tipo, ref_cod_exemplar_tipo);

ALTER TABLE ONLY pmieducar.cliente_tipo
    ADD CONSTRAINT cliente_tipo_pkey PRIMARY KEY (cod_cliente_tipo);

ALTER TABLE ONLY pmieducar.candidato_reserva_vaga
    ADD CONSTRAINT cod_candidato_reserva_vaga_pkey PRIMARY KEY (cod_candidato_reserva_vaga);

ALTER TABLE ONLY pmieducar.disciplina_dependencia
    ADD CONSTRAINT cod_disciplina_dependencia_pkey PRIMARY KEY (cod_disciplina_dependencia);

ALTER TABLE ONLY pmieducar.dispensa_disciplina
    ADD CONSTRAINT cod_dispensa_pkey PRIMARY KEY (cod_dispensa);

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT cod_serie_vaga_pkey PRIMARY KEY (cod_serie_vaga);

ALTER TABLE ONLY pmieducar.serie_vaga
    ADD CONSTRAINT cod_serie_vaga_unique UNIQUE (ano, ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, turno);

ALTER TABLE ONLY pmieducar.servidor_funcao
    ADD CONSTRAINT cod_servidor_funcao_pkey PRIMARY KEY (cod_servidor_funcao);

ALTER TABLE ONLY pmieducar.coffebreak_tipo
    ADD CONSTRAINT coffebreak_tipo_pkey PRIMARY KEY (cod_coffebreak_tipo);

ALTER TABLE ONLY pmieducar.curso
    ADD CONSTRAINT curso_pkey PRIMARY KEY (cod_curso);

ALTER TABLE ONLY pmieducar.disciplina
    ADD CONSTRAINT disciplina_pkey PRIMARY KEY (cod_disciplina);

ALTER TABLE ONLY pmieducar.disciplina_serie
    ADD CONSTRAINT disciplina_serie_pkey PRIMARY KEY (ref_cod_disciplina, ref_cod_serie);

ALTER TABLE ONLY pmieducar.disciplina_topico
    ADD CONSTRAINT disciplina_topico_pkey PRIMARY KEY (cod_disciplina_topico);

ALTER TABLE ONLY pmieducar.distribuicao_uniforme
    ADD CONSTRAINT distribuicao_uniforme_cod_distribuicao_uniforme_pkey PRIMARY KEY (cod_distribuicao_uniforme);

ALTER TABLE ONLY pmieducar.escola_ano_letivo
    ADD CONSTRAINT escola_ano_letivo_pkey PRIMARY KEY (ref_cod_escola, ano);

ALTER TABLE ONLY pmieducar.escola_complemento
    ADD CONSTRAINT escola_complemento_pkey PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY pmieducar.escola_curso
    ADD CONSTRAINT escola_curso_pkey PRIMARY KEY (ref_cod_escola, ref_cod_curso);

ALTER TABLE ONLY pmieducar.escola_localizacao
    ADD CONSTRAINT escola_localizacao_pkey PRIMARY KEY (cod_escola_localizacao);

ALTER TABLE ONLY pmieducar.escola
    ADD CONSTRAINT escola_pkey PRIMARY KEY (cod_escola);

ALTER TABLE ONLY pmieducar.escola_rede_ensino
    ADD CONSTRAINT escola_rede_ensino_pkey PRIMARY KEY (cod_escola_rede_ensino);

ALTER TABLE ONLY pmieducar.escola_serie_disciplina
    ADD CONSTRAINT escola_serie_disciplina_pkey PRIMARY KEY (ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina);

ALTER TABLE ONLY pmieducar.escola_serie
    ADD CONSTRAINT escola_serie_pkey PRIMARY KEY (ref_cod_escola, ref_cod_serie);

ALTER TABLE ONLY pmieducar.escola_usuario
    ADD CONSTRAINT escola_usuario_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.exemplar_emprestimo
    ADD CONSTRAINT exemplar_emprestimo_pkey PRIMARY KEY (cod_emprestimo);

ALTER TABLE ONLY pmieducar.exemplar
    ADD CONSTRAINT exemplar_pkey PRIMARY KEY (cod_exemplar);

ALTER TABLE ONLY pmieducar.exemplar_tipo
    ADD CONSTRAINT exemplar_tipo_pkey PRIMARY KEY (cod_exemplar_tipo);

ALTER TABLE ONLY pmieducar.falta_aluno
    ADD CONSTRAINT falta_aluno_pkey PRIMARY KEY (cod_falta_aluno);

ALTER TABLE ONLY pmieducar.falta_atraso_compensado
    ADD CONSTRAINT falta_atraso_compensado_pkey PRIMARY KEY (cod_compensado);

ALTER TABLE ONLY pmieducar.falta_atraso
    ADD CONSTRAINT falta_atraso_pkey PRIMARY KEY (cod_falta_atraso);

ALTER TABLE ONLY pmieducar.faltas
    ADD CONSTRAINT faltas_pkey PRIMARY KEY (ref_cod_matricula, sequencial);

ALTER TABLE ONLY pmieducar.bloqueio_lancamento_faltas_notas
    ADD CONSTRAINT fk_bloqueio_lancamento_faltas_notas PRIMARY KEY (cod_bloqueio);

ALTER TABLE ONLY pmieducar.fonte
    ADD CONSTRAINT fonte_pkey PRIMARY KEY (cod_fonte);

ALTER TABLE ONLY pmieducar.funcao
    ADD CONSTRAINT funcao_pkey PRIMARY KEY (cod_funcao);

ALTER TABLE ONLY pmieducar.habilitacao_curso
    ADD CONSTRAINT habilitacao_curso_pkey PRIMARY KEY (ref_cod_habilitacao, ref_cod_curso);

ALTER TABLE ONLY pmieducar.habilitacao
    ADD CONSTRAINT habilitacao_pkey PRIMARY KEY (cod_habilitacao);

ALTER TABLE ONLY pmieducar.historico_disciplinas
    ADD CONSTRAINT historico_disciplinas_pkey PRIMARY KEY (sequencial, ref_ref_cod_aluno, ref_sequencial);

ALTER TABLE ONLY pmieducar.historico_escolar
    ADD CONSTRAINT historico_escolar_pkey PRIMARY KEY (ref_cod_aluno, sequencial);

ALTER TABLE ONLY pmieducar.historico_grade_curso
    ADD CONSTRAINT historico_grade_curso_pk PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.infra_comodo_funcao
    ADD CONSTRAINT infra_comodo_funcao_pkey PRIMARY KEY (cod_infra_comodo_funcao);

ALTER TABLE ONLY pmieducar.infra_predio_comodo
    ADD CONSTRAINT infra_predio_comodo_pkey PRIMARY KEY (cod_infra_predio_comodo);

ALTER TABLE ONLY pmieducar.infra_predio
    ADD CONSTRAINT infra_predio_pkey PRIMARY KEY (cod_infra_predio);

ALTER TABLE ONLY pmieducar.instituicao_documentacao
    ADD CONSTRAINT instituicao_documentacao_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.instituicao
    ADD CONSTRAINT instituicao_pkey PRIMARY KEY (cod_instituicao);

ALTER TABLE ONLY pmieducar.material_didatico
    ADD CONSTRAINT material_didatico_pkey PRIMARY KEY (cod_material_didatico);

ALTER TABLE ONLY pmieducar.material_tipo
    ADD CONSTRAINT material_tipo_pkey PRIMARY KEY (cod_material_tipo);

ALTER TABLE ONLY pmieducar.matricula_excessao
    ADD CONSTRAINT matricula_excessao_pk PRIMARY KEY (cod_aluno_excessao);

ALTER TABLE ONLY pmieducar.matricula_ocorrencia_disciplinar
    ADD CONSTRAINT matricula_ocorrencia_disciplinar_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_tipo_ocorrencia_disciplinar, sequencial);

ALTER TABLE ONLY pmieducar.matricula
    ADD CONSTRAINT matricula_pkey PRIMARY KEY (cod_matricula);

ALTER TABLE ONLY pmieducar.matricula_turma
    ADD CONSTRAINT matricula_turma_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_turma, sequencial);

ALTER TABLE ONLY pmieducar.menu_tipo_usuario
    ADD CONSTRAINT menu_tipo_usuario_pkey PRIMARY KEY (ref_cod_tipo_usuario, ref_cod_menu_submenu);

ALTER TABLE ONLY pmieducar.modulo
    ADD CONSTRAINT modulo_pkey PRIMARY KEY (cod_modulo);

ALTER TABLE ONLY pmieducar.motivo_afastamento
    ADD CONSTRAINT motivo_afastamento_pkey PRIMARY KEY (cod_motivo_afastamento);

ALTER TABLE ONLY pmieducar.motivo_baixa
    ADD CONSTRAINT motivo_baixa_pkey PRIMARY KEY (cod_motivo_baixa);

ALTER TABLE ONLY pmieducar.motivo_suspensao
    ADD CONSTRAINT motivo_suspensao_pkey PRIMARY KEY (cod_motivo_suspensao);

ALTER TABLE ONLY pmieducar.nivel_ensino
    ADD CONSTRAINT nivel_ensino_pkey PRIMARY KEY (cod_nivel_ensino);

ALTER TABLE ONLY pmieducar.nivel
    ADD CONSTRAINT nivel_pkey PRIMARY KEY (cod_nivel);

ALTER TABLE ONLY pmieducar.nota_aluno
    ADD CONSTRAINT nota_aluno_pkey PRIMARY KEY (cod_nota_aluno);

ALTER TABLE ONLY pmieducar.operador
    ADD CONSTRAINT operador_pkey PRIMARY KEY (cod_operador);

ALTER TABLE ONLY pmieducar.pagamento_multa
    ADD CONSTRAINT pagamento_multa_pkey PRIMARY KEY (cod_pagamento_multa);

ALTER TABLE ONLY pmieducar.abandono_tipo
    ADD CONSTRAINT pk_cod_abandono_tipo PRIMARY KEY (cod_abandono_tipo);

ALTER TABLE ONLY pmieducar.bloqueio_ano_letivo
    ADD CONSTRAINT pmieducar_bloqueio_ano_letivo_pkey PRIMARY KEY (ref_cod_instituicao, ref_ano);

ALTER TABLE ONLY pmieducar.projeto_aluno
    ADD CONSTRAINT pmieducar_projeto_aluno_pk PRIMARY KEY (ref_cod_projeto, ref_cod_aluno);

ALTER TABLE ONLY pmieducar.projeto
    ADD CONSTRAINT pmieducar_projeto_cod_projeto PRIMARY KEY (cod_projeto);

ALTER TABLE ONLY pmieducar.pre_requisito
    ADD CONSTRAINT pre_requisito_pkey PRIMARY KEY (cod_pre_requisito);

ALTER TABLE ONLY pmieducar.quadro_horario_horarios_aux
    ADD CONSTRAINT quadro_horario_horarios_aux_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);

ALTER TABLE ONLY pmieducar.quadro_horario_horarios
    ADD CONSTRAINT quadro_horario_horarios_pkey PRIMARY KEY (ref_cod_quadro_horario, sequencial);

ALTER TABLE ONLY pmieducar.quadro_horario
    ADD CONSTRAINT quadro_horario_pkey PRIMARY KEY (cod_quadro_horario);

ALTER TABLE ONLY pmieducar.quantidade_reserva_externa
    ADD CONSTRAINT quantidade_reserva_externa_pkey PRIMARY KEY (ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie, ref_turma_turno_id, ano);

ALTER TABLE ONLY pmieducar.religiao
    ADD CONSTRAINT religiao_pkey PRIMARY KEY (cod_religiao);

ALTER TABLE ONLY pmieducar.reserva_vaga
    ADD CONSTRAINT reserva_vaga_pkey PRIMARY KEY (cod_reserva_vaga);

ALTER TABLE ONLY pmieducar.reservas
    ADD CONSTRAINT reservas_pkey PRIMARY KEY (cod_reserva);

ALTER TABLE ONLY pmieducar.sequencia_serie
    ADD CONSTRAINT sequencia_serie_pkey PRIMARY KEY (ref_serie_origem, ref_serie_destino);

ALTER TABLE ONLY pmieducar.serie
    ADD CONSTRAINT serie_pkey PRIMARY KEY (cod_serie);

ALTER TABLE ONLY pmieducar.serie_pre_requisito
    ADD CONSTRAINT serie_pre_requisito_pkey PRIMARY KEY (ref_cod_pre_requisito, ref_cod_operador, ref_cod_serie);

ALTER TABLE ONLY pmieducar.servidor_afastamento
    ADD CONSTRAINT servidor_afastamento_pkey PRIMARY KEY (ref_cod_servidor, sequencial, ref_ref_cod_instituicao);

ALTER TABLE ONLY pmieducar.servidor_alocacao
    ADD CONSTRAINT servidor_alocacao_pkey PRIMARY KEY (cod_servidor_alocacao);

ALTER TABLE ONLY pmieducar.servidor_curso
    ADD CONSTRAINT servidor_curso_pkey PRIMARY KEY (cod_servidor_curso);

ALTER TABLE ONLY pmieducar.servidor_curso_ministra
    ADD CONSTRAINT servidor_cuso_ministra_pkey PRIMARY KEY (ref_cod_curso, ref_ref_cod_instituicao, ref_cod_servidor);

ALTER TABLE ONLY pmieducar.servidor_disciplina
    ADD CONSTRAINT servidor_disciplina_pkey PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_curso);

ALTER TABLE ONLY pmieducar.servidor_formacao
    ADD CONSTRAINT servidor_formacao_pkey PRIMARY KEY (cod_formacao);

ALTER TABLE ONLY pmieducar.servidor
    ADD CONSTRAINT servidor_pkey PRIMARY KEY (cod_servidor, ref_cod_instituicao);

ALTER TABLE ONLY pmieducar.servidor_titulo_concurso
    ADD CONSTRAINT servidor_titulo_concurso_pkey PRIMARY KEY (cod_servidor_titulo);

ALTER TABLE ONLY pmieducar.situacao
    ADD CONSTRAINT situacao_pkey PRIMARY KEY (cod_situacao);

ALTER TABLE ONLY pmieducar.subnivel
    ADD CONSTRAINT subnivel_pkey PRIMARY KEY (cod_subnivel);

ALTER TABLE ONLY pmieducar.tipo_avaliacao
    ADD CONSTRAINT tipo_avaliacao_pkey PRIMARY KEY (cod_tipo_avaliacao);

ALTER TABLE ONLY pmieducar.tipo_avaliacao_valores
    ADD CONSTRAINT tipo_avaliacao_valores_pkey PRIMARY KEY (ref_cod_tipo_avaliacao, sequencial);

ALTER TABLE ONLY pmieducar.tipo_dispensa
    ADD CONSTRAINT tipo_dispensa_pkey PRIMARY KEY (cod_tipo_dispensa);

ALTER TABLE ONLY pmieducar.tipo_ensino
    ADD CONSTRAINT tipo_ensino_pkey PRIMARY KEY (cod_tipo_ensino);

ALTER TABLE ONLY pmieducar.tipo_ocorrencia_disciplinar
    ADD CONSTRAINT tipo_ocorrencia_disciplinar_pkey PRIMARY KEY (cod_tipo_ocorrencia_disciplinar);

ALTER TABLE ONLY pmieducar.tipo_regime
    ADD CONSTRAINT tipo_regime_pkey PRIMARY KEY (cod_tipo_regime);

ALTER TABLE ONLY pmieducar.tipo_usuario
    ADD CONSTRAINT tipo_usuario_pkey PRIMARY KEY (cod_tipo_usuario);

ALTER TABLE ONLY pmieducar.transferencia_solicitacao
    ADD CONSTRAINT transferencia_solicitacao_pkey PRIMARY KEY (cod_transferencia_solicitacao);

ALTER TABLE ONLY pmieducar.transferencia_tipo
    ADD CONSTRAINT transferencia_tipo_pkey PRIMARY KEY (cod_transferencia_tipo);

ALTER TABLE ONLY pmieducar.turma_modulo
    ADD CONSTRAINT turma_modulo_pkey PRIMARY KEY (ref_cod_turma, ref_cod_modulo, sequencial);

ALTER TABLE ONLY pmieducar.turma
    ADD CONSTRAINT turma_pkey PRIMARY KEY (cod_turma);

ALTER TABLE ONLY pmieducar.turma_tipo
    ADD CONSTRAINT turma_tipo_pkey PRIMARY KEY (cod_turma_tipo);

ALTER TABLE ONLY pmieducar.turma_turno
    ADD CONSTRAINT turma_turno_pkey PRIMARY KEY (id);

ALTER TABLE ONLY pmieducar.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (cod_usuario);

ALTER TABLE ONLY pmiotopic.funcionario_su
    ADD CONSTRAINT funcionario_su_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj);

ALTER TABLE ONLY pmiotopic.grupomoderador
    ADD CONSTRAINT grupomoderador_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_grupos);

ALTER TABLE ONLY pmiotopic.grupopessoa
    ADD CONSTRAINT grupopessoa_pkey PRIMARY KEY (ref_idpes, ref_cod_grupos);

ALTER TABLE ONLY pmiotopic.grupos
    ADD CONSTRAINT grupos_pkey PRIMARY KEY (cod_grupos);

ALTER TABLE ONLY pmiotopic.notas
    ADD CONSTRAINT notas_pkey PRIMARY KEY (sequencial, ref_idpes);

ALTER TABLE ONLY pmiotopic.participante
    ADD CONSTRAINT participante_pkey PRIMARY KEY (sequencial, ref_ref_cod_grupos, ref_ref_idpes, ref_cod_reuniao);

ALTER TABLE ONLY pmiotopic.reuniao
    ADD CONSTRAINT reuniao_pkey PRIMARY KEY (cod_reuniao);

ALTER TABLE ONLY pmiotopic.topico
    ADD CONSTRAINT topico_pkey PRIMARY KEY (cod_topico);

ALTER TABLE ONLY pmiotopic.topicoreuniao
    ADD CONSTRAINT topicoreuniao_pkey PRIMARY KEY (ref_cod_topico, ref_cod_reuniao);

ALTER TABLE ONLY portal.acesso
    ADD CONSTRAINT acesso_pk PRIMARY KEY (cod_acesso);

ALTER TABLE ONLY portal.agenda_compromisso
    ADD CONSTRAINT agenda_compromisso_pkey PRIMARY KEY (cod_agenda_compromisso, versao, ref_cod_agenda);

ALTER TABLE ONLY portal.agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (cod_agenda);

ALTER TABLE ONLY portal.agenda_pref
    ADD CONSTRAINT agenda_pref_pk PRIMARY KEY (cod_comp);

ALTER TABLE ONLY portal.agenda_responsavel
    ADD CONSTRAINT agenda_responsavel_pkey PRIMARY KEY (ref_cod_agenda, ref_ref_cod_pessoa_fj);

ALTER TABLE ONLY portal.compras_editais_editais_empresas
    ADD CONSTRAINT compras_editais_editais_empresas_pk PRIMARY KEY (ref_cod_compras_editais_editais, ref_cod_compras_editais_empresa, data_hora);

ALTER TABLE ONLY portal.compras_editais_editais
    ADD CONSTRAINT compras_editais_editais_pk PRIMARY KEY (cod_compras_editais_editais);

ALTER TABLE ONLY portal.compras_editais_empresa
    ADD CONSTRAINT compras_editais_empresa_pk PRIMARY KEY (cod_compras_editais_empresa);

ALTER TABLE ONLY portal.compras_final_pregao
    ADD CONSTRAINT compras_final_pregao_pk PRIMARY KEY (cod_compras_final_pregao);

ALTER TABLE ONLY portal.compras_funcionarios
    ADD CONSTRAINT compras_funcionarios_pk PRIMARY KEY (ref_ref_cod_pessoa_fj);

ALTER TABLE ONLY portal.compras_licitacoes
    ADD CONSTRAINT compras_licitacoes_pk PRIMARY KEY (cod_compras_licitacoes);

ALTER TABLE ONLY portal.compras_modalidade
    ADD CONSTRAINT compras_modalidade_pk PRIMARY KEY (cod_compras_modalidade);

ALTER TABLE ONLY portal.compras_pregao_execucao
    ADD CONSTRAINT compras_pregao_execucao_pk PRIMARY KEY (cod_compras_pregao_execucao);

ALTER TABLE ONLY portal.compras_prestacao_contas
    ADD CONSTRAINT compras_prestacao_contas_pk PRIMARY KEY (cod_compras_prestacao_contas);

ALTER TABLE ONLY portal.foto_portal
    ADD CONSTRAINT foto_portal_pk PRIMARY KEY (cod_foto_portal);

ALTER TABLE ONLY portal.foto_secao
    ADD CONSTRAINT foto_secao_pk PRIMARY KEY (cod_foto_secao);

ALTER TABLE ONLY portal.funcionario
    ADD CONSTRAINT funcionario_pk PRIMARY KEY (ref_cod_pessoa_fj);

ALTER TABLE ONLY portal.funcionario_vinculo
    ADD CONSTRAINT funcionario_vinculo_pk PRIMARY KEY (cod_funcionario_vinculo);

ALTER TABLE ONLY portal.imagem
    ADD CONSTRAINT imagem_pkey PRIMARY KEY (cod_imagem);

ALTER TABLE ONLY portal.imagem_tipo
    ADD CONSTRAINT imagem_tipo_pkey PRIMARY KEY (cod_imagem_tipo);

ALTER TABLE ONLY portal.intranet_segur_permissao_negada
    ADD CONSTRAINT intranet_segur_permissao_negada_pk PRIMARY KEY (cod_intranet_segur_permissao_negada);

ALTER TABLE ONLY portal.jor_arquivo
    ADD CONSTRAINT jor_arquivo_pk PRIMARY KEY (ref_cod_jor_edicao, jor_arquivo);

ALTER TABLE ONLY portal.jor_edicao
    ADD CONSTRAINT jor_edicao_pk PRIMARY KEY (cod_jor_edicao);

ALTER TABLE ONLY portal.mailling_email_conteudo
    ADD CONSTRAINT mailling_email_conteudo_pk PRIMARY KEY (cod_mailling_email_conteudo);

ALTER TABLE ONLY portal.mailling_email
    ADD CONSTRAINT mailling_email_pk PRIMARY KEY (cod_mailling_email);

ALTER TABLE ONLY portal.mailling_fila_envio
    ADD CONSTRAINT mailling_fila_envio_pk PRIMARY KEY (cod_mailling_fila_envio);

ALTER TABLE ONLY portal.mailling_grupo_email
    ADD CONSTRAINT mailling_grupo_email_pk PRIMARY KEY (ref_cod_mailling_email, ref_cod_mailling_grupo);

ALTER TABLE ONLY portal.mailling_grupo
    ADD CONSTRAINT mailling_grupo_pk PRIMARY KEY (cod_mailling_grupo);

ALTER TABLE ONLY portal.mailling_historico
    ADD CONSTRAINT mailling_historico_pk PRIMARY KEY (cod_mailling_historico);

ALTER TABLE ONLY portal.menu_funcionario
    ADD CONSTRAINT menu_funcionario_pk PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_menu_submenu);

ALTER TABLE ONLY portal.menu_menu
    ADD CONSTRAINT menu_menu_pk PRIMARY KEY (cod_menu_menu);

ALTER TABLE ONLY portal.menu_submenu
    ADD CONSTRAINT menu_submenu_pk PRIMARY KEY (cod_menu_submenu);

ALTER TABLE ONLY portal.not_portal
    ADD CONSTRAINT not_portal_pk PRIMARY KEY (cod_not_portal);

ALTER TABLE ONLY portal.not_portal_tipo
    ADD CONSTRAINT not_portal_tipo_pk PRIMARY KEY (ref_cod_not_portal, ref_cod_not_tipo);

ALTER TABLE ONLY portal.not_tipo
    ADD CONSTRAINT not_tipo_pk PRIMARY KEY (cod_not_tipo);

ALTER TABLE ONLY portal.not_vinc_portal
    ADD CONSTRAINT not_vinc_portal_pk PRIMARY KEY (ref_cod_not_portal, vic_num);

ALTER TABLE ONLY portal.pessoa_atividade
    ADD CONSTRAINT pessoa_atividade_pk PRIMARY KEY (cod_pessoa_atividade);

ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
    ADD CONSTRAINT pessoa_fj_pessoa_atividade_pk PRIMARY KEY (ref_cod_pessoa_atividade, ref_cod_pessoa_fj);

ALTER TABLE ONLY portal.pessoa_fj
    ADD CONSTRAINT pessoa_fj_pk PRIMARY KEY (cod_pessoa_fj);

ALTER TABLE ONLY portal.pessoa_ramo_atividade
    ADD CONSTRAINT pessoa_ramo_atividade_pk PRIMARY KEY (cod_ramo_atividade);

ALTER TABLE ONLY portal.portal_concurso
    ADD CONSTRAINT portal_concurso_pk PRIMARY KEY (cod_portal_concurso);

ALTER TABLE ONLY public.bairro_regiao
    ADD CONSTRAINT bairro_regiao_pkey PRIMARY KEY (ref_cod_regiao, ref_idbai);

ALTER TABLE ONLY public.pghero_query_stats
    ADD CONSTRAINT pghero_query_stats_pkey PRIMARY KEY (id);

ALTER TABLE ONLY public.phinxlog
    ADD CONSTRAINT phinxlog_pkey PRIMARY KEY (version);

ALTER TABLE ONLY public.bairro
    ADD CONSTRAINT pk_bairro PRIMARY KEY (idbai);

ALTER TABLE ONLY public.distrito
    ADD CONSTRAINT pk_distrito PRIMARY KEY (iddis);

ALTER TABLE ONLY public.logradouro
    ADD CONSTRAINT pk_logradouro PRIMARY KEY (idlog);

ALTER TABLE ONLY public.logradouro_fonetico
    ADD CONSTRAINT pk_logradouro_fonetico PRIMARY KEY (fonema, idlog);

ALTER TABLE ONLY public.municipio
    ADD CONSTRAINT pk_municipio PRIMARY KEY (idmun);

ALTER TABLE ONLY public.pais
    ADD CONSTRAINT pk_pais PRIMARY KEY (idpais);

ALTER TABLE ONLY public.setor
    ADD CONSTRAINT pk_setor PRIMARY KEY (idset);

ALTER TABLE ONLY public.setor_bai
    ADD CONSTRAINT pk_setorbai PRIMARY KEY (idsetorbai);

ALTER TABLE ONLY public.uf
    ADD CONSTRAINT pk_uf PRIMARY KEY (sigla_uf);

ALTER TABLE ONLY public.vila
    ADD CONSTRAINT pk_vila PRIMARY KEY (idvil);

ALTER TABLE ONLY public.changelog
    ADD CONSTRAINT pkchangelog PRIMARY KEY (change_number, delta_set);

ALTER TABLE ONLY public.regiao
    ADD CONSTRAINT regiao_pkey PRIMARY KEY (cod_regiao);

ALTER TABLE ONLY serieciasc.aluno_uniforme
    ADD CONSTRAINT aluno_uniforme_ref_cod_aluno_pk PRIMARY KEY (ref_cod_aluno, data_recebimento);

ALTER TABLE ONLY serieciasc.aluno_cod_aluno
    ADD CONSTRAINT cod_aluno_serie_ref_cod_aluno_pk PRIMARY KEY (cod_aluno, cod_ciasc);

ALTER TABLE ONLY serieciasc.escola_regulamentacao
    ADD CONSTRAINT educacenso_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY serieciasc.escola_agua
    ADD CONSTRAINT escola_agua_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY serieciasc.escola_energia
    ADD CONSTRAINT escola_energia_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY serieciasc.escola_lingua_indigena
    ADD CONSTRAINT escola_lingua_indigena_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY serieciasc.escola_lixo
    ADD CONSTRAINT escola_lixo_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY serieciasc.escola_projeto
    ADD CONSTRAINT escola_projeto_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY serieciasc.escola_sanitario
    ADD CONSTRAINT escola_sanitario_ref_cod_escola_pk PRIMARY KEY (ref_cod_escola);

ALTER TABLE ONLY urbano.cep_logradouro
    ADD CONSTRAINT pk_cep_logradouro PRIMARY KEY (cep, idlog);

ALTER TABLE ONLY urbano.cep_logradouro_bairro
    ADD CONSTRAINT pk_cep_logradouro_bairro PRIMARY KEY (idbai, idlog, cep);

ALTER TABLE ONLY urbano.tipo_logradouro
    ADD CONSTRAINT pk_tipo_logradouro PRIMARY KEY (idtlog);

CREATE UNIQUE INDEX un_usuario_idpes ON acesso.usuario USING btree (idpes);

CREATE UNIQUE INDEX un_baixa_guia_remessa ON alimentos.baixa_guia_remessa USING btree (idgui, dt_recebimento);

CREATE UNIQUE INDEX un_cardapio_produto ON alimentos.cardapio_produto USING btree (idcar, idpro);

CREATE UNIQUE INDEX un_cliente ON alimentos.cliente USING btree (idcli, identificacao);

CREATE UNIQUE INDEX un_contrato ON alimentos.contrato USING btree (idcli, codigo, num_aditivo);

CREATE UNIQUE INDEX un_contrato_produto ON alimentos.contrato_produto USING btree (idcon, idpro);

CREATE UNIQUE INDEX un_evento ON alimentos.evento USING btree (idcad, mes, dia);

CREATE UNIQUE INDEX un_faixa_cp_quimico ON alimentos.faixa_composto_quimico USING btree (idcom, idfae);

CREATE UNIQUE INDEX un_fornecedor ON alimentos.fornecedor USING btree (idcli, nome_fantasia);

CREATE UNIQUE INDEX un_fornecedor_unidade_atend ON alimentos.fornecedor_unidade_atendida USING btree (iduni, idfor);

CREATE UNIQUE INDEX un_guia_remessa ON alimentos.guia_remessa USING btree (idcli, ano, sequencial);

CREATE UNIQUE INDEX un_guia_remessa_produto ON alimentos.guia_remessa_produto USING btree (idgui, idpro);

CREATE UNIQUE INDEX un_prod_cp_quimico ON alimentos.produto_composto_quimico USING btree (idpro, idcom);

CREATE UNIQUE INDEX un_produto ON alimentos.produto USING btree (idcli, nome_compra);

CREATE UNIQUE INDEX un_produto_fornecedor ON alimentos.produto_fornecedor USING btree (idfor, idpro);

CREATE UNIQUE INDEX un_produto_medida_caseira ON alimentos.produto_medida_caseira USING btree (idmedcas, idcli, idpro);

CREATE UNIQUE INDEX un_rec_cp_quimico ON alimentos.receita_composto_quimico USING btree (idcom, idrec);

CREATE UNIQUE INDEX un_rec_prod ON alimentos.receita_produto USING btree (idpro, idrec);

CREATE UNIQUE INDEX un_uni_faixa_etaria ON alimentos.unidade_faixa_etaria USING btree (iduni, idfae);

CREATE UNIQUE INDEX un_unidade_atendida ON alimentos.unidade_atendida USING btree (idcli, codigo);

CREATE UNIQUE INDEX un_fisica_cpf ON cadastro.fisica_cpf USING btree (cpf);

CREATE UNIQUE INDEX un_juridica_cnpj ON cadastro.juridica USING btree (cnpj);

CREATE UNIQUE INDEX alunocomponenteetapa ON modules.parecer_componente_curricular USING btree (parecer_aluno_id, componente_curricular_id, etapa);

CREATE INDEX area_conhecimento_nome_key ON modules.area_conhecimento USING btree (nome);

CREATE INDEX componente_curricular_area_conhecimento_key ON modules.componente_curricular USING btree (area_conhecimento_id);

CREATE UNIQUE INDEX componente_curricular_id_key ON modules.componente_curricular USING btree (id);

CREATE INDEX componente_curricular_turma_turma_idx ON modules.componente_curricular_turma USING btree (turma_id);

CREATE INDEX docente_licenciatura_ies_idx ON modules.docente_licenciatura USING btree (ies_id);

CREATE INDEX idx_educacenso_ies_ies_id ON modules.educacenso_ies USING btree (ies_id);

CREATE INDEX idx_falta_aluno_matricula_id ON modules.falta_aluno USING btree (matricula_id);

CREATE INDEX idx_falta_aluno_matricula_id_tipo ON modules.falta_aluno USING btree (matricula_id, tipo_falta);

CREATE INDEX idx_falta_componente_curricular_id1 ON modules.falta_componente_curricular USING btree (falta_aluno_id, componente_curricular_id, etapa);

CREATE INDEX idx_falta_geral_falta_aluno_id ON modules.falta_geral USING btree (falta_aluno_id);

CREATE INDEX idx_nota_aluno_matricula ON modules.nota_aluno USING btree (matricula_id);

CREATE INDEX idx_nota_aluno_matricula_id ON modules.nota_aluno USING btree (id, matricula_id);

CREATE INDEX idx_nota_componente_curricular_etapa ON modules.nota_componente_curricular USING btree (nota_aluno_id, componente_curricular_id, etapa);

CREATE INDEX idx_nota_componente_curricular_etp ON modules.nota_componente_curricular USING btree (componente_curricular_id, etapa);

CREATE INDEX idx_nota_componente_curricular_id ON modules.nota_componente_curricular USING btree (componente_curricular_id);

CREATE INDEX idx_parecer_aluno_matricula_id ON modules.parecer_aluno USING btree (matricula_id);

CREATE INDEX idx_parecer_geral_parecer_aluno_etp ON modules.parecer_geral USING btree (parecer_aluno_id, etapa);

CREATE INDEX idx_tabela_arredondamento_valor_tabela_id ON modules.tabela_arredondamento_valor USING btree (tabela_arredondamento_id);

CREATE UNIQUE INDEX regra_avaliacao_id_key ON modules.regra_avaliacao USING btree (id);

CREATE UNIQUE INDEX tabela_arredondamento_id_key ON modules.tabela_arredondamento USING btree (id);

CREATE INDEX exemplar_tombo_idx ON pmieducar.exemplar USING btree (tombo);

CREATE INDEX fki_biblioteca_usuario_ref_cod_biblioteca_fk ON pmieducar.biblioteca_usuario USING btree (ref_cod_biblioteca);

CREATE INDEX fki_servidor_ref_cod_subnivel ON pmieducar.servidor USING btree (ref_cod_subnivel);

CREATE INDEX fki_servidor_ref_cod_subnivel_ ON pmieducar.servidor USING btree (ref_cod_subnivel);

CREATE INDEX historico_escolar_ano_idx ON pmieducar.historico_escolar USING btree (ano);

CREATE INDEX historico_escolar_ativo_idx ON pmieducar.historico_escolar USING btree (ativo);

CREATE INDEX historico_escolar_nm_serie_idx ON pmieducar.historico_escolar USING btree (nm_serie);

CREATE INDEX i_aluno_ativo ON pmieducar.aluno USING btree (ativo);

CREATE INDEX i_aluno_beneficio_ativo ON pmieducar.aluno_beneficio USING btree (ativo);

CREATE INDEX i_aluno_beneficio_nm_beneficio ON pmieducar.aluno_beneficio USING btree (nm_beneficio);

CREATE INDEX i_aluno_beneficio_ref_usuario_cad ON pmieducar.aluno_beneficio USING btree (ref_usuario_cad);

CREATE INDEX i_aluno_ref_cod_religiao ON pmieducar.aluno USING btree (ref_cod_religiao);

CREATE INDEX i_aluno_ref_idpes ON pmieducar.aluno USING btree (ref_idpes);

CREATE INDEX i_aluno_ref_usuario_cad ON pmieducar.aluno USING btree (ref_usuario_cad);

CREATE INDEX i_calendario_ano_letivo_ano ON pmieducar.calendario_ano_letivo USING btree (ano);

CREATE INDEX i_calendario_ano_letivo_ativo ON pmieducar.calendario_ano_letivo USING btree (ativo);

CREATE INDEX i_calendario_ano_letivo_ref_cod_escola ON pmieducar.calendario_ano_letivo USING btree (ref_cod_escola);

CREATE INDEX i_calendario_ano_letivo_ref_usuario_cad ON pmieducar.calendario_ano_letivo USING btree (ref_usuario_cad);

CREATE INDEX i_calendario_dia_ativo ON pmieducar.calendario_dia USING btree (ativo);

CREATE INDEX i_calendario_dia_dia ON pmieducar.calendario_dia USING btree (dia);

CREATE INDEX i_calendario_dia_mes ON pmieducar.calendario_dia USING btree (mes);

CREATE INDEX i_calendario_dia_motivo_ativo ON pmieducar.calendario_dia_motivo USING btree (ativo);

CREATE INDEX i_calendario_dia_motivo_ref_cod_escola ON pmieducar.calendario_dia_motivo USING btree (ref_cod_escola);

CREATE INDEX i_calendario_dia_motivo_ref_usuario_cad ON pmieducar.calendario_dia_motivo USING btree (ref_usuario_cad);

CREATE INDEX i_calendario_dia_motivo_sigla ON pmieducar.calendario_dia_motivo USING btree (sigla);

CREATE INDEX i_calendario_dia_motivo_tipo ON pmieducar.calendario_dia_motivo USING btree (tipo);

CREATE INDEX i_calendario_dia_ref_cod_calendario_dia_motivo ON pmieducar.calendario_dia USING btree (ref_cod_calendario_dia_motivo);

CREATE INDEX i_calendario_dia_ref_usuario_cad ON pmieducar.calendario_dia USING btree (ref_usuario_cad);

CREATE INDEX i_coffebreak_tipo_ativo ON pmieducar.coffebreak_tipo USING btree (ativo);

CREATE INDEX i_coffebreak_tipo_custo_unitario ON pmieducar.coffebreak_tipo USING btree (custo_unitario);

CREATE INDEX i_coffebreak_tipo_nm_tipo ON pmieducar.coffebreak_tipo USING btree (nm_tipo);

CREATE INDEX i_coffebreak_tipo_ref_usuario_cad ON pmieducar.coffebreak_tipo USING btree (ref_usuario_cad);

CREATE INDEX i_curso_ativo ON pmieducar.curso USING btree (ativo);

CREATE INDEX i_curso_ato_poder_publico ON pmieducar.curso USING btree (ato_poder_publico);

CREATE INDEX i_curso_carga_horaria ON pmieducar.curso USING btree (carga_horaria);

CREATE INDEX i_curso_nm_curso ON pmieducar.curso USING btree (nm_curso);

CREATE INDEX i_curso_objetivo_curso ON pmieducar.curso USING btree (objetivo_curso);

CREATE INDEX i_curso_qtd_etapas ON pmieducar.curso USING btree (qtd_etapas);

CREATE INDEX i_curso_ref_cod_nivel_ensino ON pmieducar.curso USING btree (ref_cod_nivel_ensino);

CREATE INDEX i_curso_ref_cod_tipo_ensino ON pmieducar.curso USING btree (ref_cod_tipo_ensino);

CREATE INDEX i_curso_ref_cod_tipo_regime ON pmieducar.curso USING btree (ref_cod_tipo_regime);

CREATE INDEX i_curso_ref_usuario_cad ON pmieducar.curso USING btree (ref_usuario_cad);

CREATE INDEX i_curso_sgl_curso ON pmieducar.curso USING btree (sgl_curso);

CREATE INDEX i_disciplina_abreviatura ON pmieducar.disciplina USING btree (abreviatura);

CREATE INDEX i_disciplina_apura_falta ON pmieducar.disciplina USING btree (apura_falta);

CREATE INDEX i_disciplina_ativo ON pmieducar.disciplina USING btree (ativo);

CREATE INDEX i_disciplina_carga_horaria ON pmieducar.disciplina USING btree (carga_horaria);

CREATE INDEX i_disciplina_nm_disciplina ON pmieducar.disciplina USING btree (nm_disciplina);

CREATE INDEX i_disciplina_ref_usuario_cad ON pmieducar.disciplina USING btree (ref_usuario_cad);

CREATE INDEX i_disciplina_topico_ativo ON pmieducar.disciplina_topico USING btree (ativo);

CREATE INDEX i_disciplina_topico_nm_topico ON pmieducar.disciplina_topico USING btree (nm_topico);

CREATE INDEX i_disciplina_topico_ref_usuario_cad ON pmieducar.disciplina_topico USING btree (ref_usuario_cad);

CREATE INDEX i_dispensa_disciplina_ref_cod_matricula ON pmieducar.dispensa_disciplina USING btree (ref_cod_matricula);

CREATE INDEX i_escola_ativo ON pmieducar.escola USING btree (ativo);

CREATE INDEX i_escola_complemento_ativo ON pmieducar.escola_complemento USING btree (ativo);

CREATE INDEX i_escola_complemento_bairro ON pmieducar.escola_complemento USING btree (bairro);

CREATE INDEX i_escola_complemento_cep ON pmieducar.escola_complemento USING btree (cep);

CREATE INDEX i_escola_complemento_complemento ON pmieducar.escola_complemento USING btree (complemento);

CREATE INDEX i_escola_complemento_email ON pmieducar.escola_complemento USING btree (email);

CREATE INDEX i_escola_complemento_logradouro ON pmieducar.escola_complemento USING btree (logradouro);

CREATE INDEX i_escola_complemento_municipio ON pmieducar.escola_complemento USING btree (municipio);

CREATE INDEX i_escola_complemento_nm_escola ON pmieducar.escola_complemento USING btree (nm_escola);

CREATE INDEX i_escola_complemento_numero ON pmieducar.escola_complemento USING btree (numero);

CREATE INDEX i_escola_complemento_ref_usuario_cad ON pmieducar.escola_complemento USING btree (ref_usuario_cad);

CREATE INDEX i_escola_curso_ativo ON pmieducar.escola_curso USING btree (ativo);

CREATE INDEX i_escola_curso_ref_usuario_cad ON pmieducar.escola_curso USING btree (ref_usuario_cad);

CREATE INDEX i_escola_localizacao_ativo ON pmieducar.escola_localizacao USING btree (ativo);

CREATE INDEX i_escola_localizacao_nm_localizacao ON pmieducar.escola_localizacao USING btree (nm_localizacao);

CREATE INDEX i_escola_localizacao_ref_usuario_cad ON pmieducar.escola_localizacao USING btree (ref_usuario_cad);

CREATE INDEX i_escola_rede_ensino_ativo ON pmieducar.escola_rede_ensino USING btree (ativo);

CREATE INDEX i_escola_rede_ensino_nm_rede ON pmieducar.escola_rede_ensino USING btree (nm_rede);

CREATE INDEX i_escola_rede_ensino_ref_usuario_cad ON pmieducar.escola_rede_ensino USING btree (ref_usuario_cad);

CREATE INDEX i_escola_ref_cod_escola_rede_ensino ON pmieducar.escola USING btree (ref_cod_escola_rede_ensino);

CREATE INDEX i_escola_ref_cod_instituicao ON pmieducar.escola USING btree (ref_cod_instituicao);

CREATE INDEX i_escola_ref_idpes ON pmieducar.escola USING btree (ref_idpes);

CREATE INDEX i_escola_ref_usuario_cad ON pmieducar.escola USING btree (ref_usuario_cad);

CREATE INDEX i_escola_serie_ensino_ativo ON pmieducar.escola_serie USING btree (ativo);

CREATE INDEX i_escola_serie_hora_final ON pmieducar.escola_serie USING btree (hora_final);

CREATE INDEX i_escola_serie_hora_inicial ON pmieducar.escola_serie USING btree (hora_inicial);

CREATE INDEX i_escola_serie_ref_usuario_cad ON pmieducar.escola_serie USING btree (ref_usuario_cad);

CREATE INDEX i_escola_sigla ON pmieducar.escola USING btree (sigla);

CREATE INDEX i_funcao_abreviatura ON pmieducar.funcao USING btree (abreviatura);

CREATE INDEX i_funcao_ativo ON pmieducar.funcao USING btree (ativo);

CREATE INDEX i_funcao_nm_funcao ON pmieducar.funcao USING btree (nm_funcao);

CREATE INDEX i_funcao_professor ON pmieducar.funcao USING btree (professor);

CREATE INDEX i_funcao_ref_usuario_cad ON pmieducar.funcao USING btree (ref_usuario_cad);

CREATE INDEX i_habilitacao_ativo ON pmieducar.habilitacao USING btree (ativo);

CREATE INDEX i_habilitacao_nm_tipo ON pmieducar.habilitacao USING btree (nm_tipo);

CREATE INDEX i_habilitacao_ref_usuario_cad ON pmieducar.habilitacao USING btree (ref_usuario_cad);

CREATE INDEX i_matricula_turma_ref_cod_turma ON pmieducar.matricula_turma USING btree (ref_cod_turma);

CREATE INDEX i_nota_aluno_ref_cod_matricula ON pmieducar.nota_aluno USING btree (ref_cod_matricula);

CREATE INDEX i_turma_nm_turma ON pmieducar.turma USING btree (nm_turma);

CREATE INDEX idx_historico_disciplinas_id ON pmieducar.historico_disciplinas USING btree (sequencial, ref_ref_cod_aluno, ref_sequencial);

CREATE INDEX idx_historico_disciplinas_id1 ON pmieducar.historico_disciplinas USING btree (ref_ref_cod_aluno, ref_sequencial);

CREATE INDEX idx_historico_escolar_aluno_ativo ON pmieducar.historico_escolar USING btree (ref_cod_aluno, ativo);

CREATE INDEX idx_historico_escolar_id1 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial);

CREATE INDEX idx_historico_escolar_id2 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial, ano);

CREATE INDEX idx_historico_escolar_id3 ON pmieducar.historico_escolar USING btree (ref_cod_aluno, ano);

CREATE INDEX idx_matricula_cod_escola_aluno ON pmieducar.matricula USING btree (ref_ref_cod_escola, ref_cod_aluno);

CREATE INDEX idx_serie_cod_regra_avaliacao_id ON pmieducar.serie USING btree (cod_serie, regra_avaliacao_id);

CREATE INDEX idx_serie_regra_avaliacao_id ON pmieducar.serie USING btree (regra_avaliacao_id);

CREATE INDEX matricula_ano_idx ON pmieducar.matricula USING btree (ano);

CREATE INDEX matricula_ativo_idx ON pmieducar.matricula USING btree (ativo);

CREATE INDEX quadro_horario_horarios_busca_horarios_idx ON pmieducar.quadro_horario_horarios USING btree (ref_servidor, ref_cod_instituicao_servidor, dia_semana, hora_inicial, hora_final, ativo);
COMMENT ON INDEX pmieducar.quadro_horario_horarios_busca_horarios_idx IS 'Índice para otimizar a busca por professores na criação de quadro de horários.';

CREATE INDEX servidor_alocacao_busca_horarios_idx ON pmieducar.servidor_alocacao USING btree (ref_ref_cod_instituicao, ref_cod_escola, ativo, periodo, carga_horaria);

CREATE INDEX servidor_idx ON pmieducar.servidor USING btree (cod_servidor, ref_cod_instituicao, ativo);
COMMENT ON INDEX pmieducar.servidor_idx IS 'Índice para otimização de acesso aos campos mais usados para queries na tabela.';

CREATE INDEX mailling_fila_envio_data_envio_idx ON portal.mailling_fila_envio USING btree (data_envio);

CREATE INDEX mailling_fila_envio_ref_cod_mailling_email ON portal.mailling_fila_envio USING btree (ref_cod_mailling_email);

CREATE INDEX mailling_fila_envio_ref_cod_mailling_email_conteudo ON portal.mailling_fila_envio USING btree (ref_cod_mailling_email_conteudo);

CREATE INDEX mailling_fila_envio_ref_cod_mailling_fila_envio ON portal.mailling_fila_envio USING btree (cod_mailling_fila_envio);

CREATE INDEX pghero_query_stats_database_captured_at_idx ON public.pghero_query_stats USING btree (database, captured_at);
