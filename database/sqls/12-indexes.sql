
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
