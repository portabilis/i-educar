  -- //

  --
  -- Cria índices em tabelas para otimizar consultas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @author   Samuel Brognoli
  -- @license  @@license@@
  -- @version  $Id$

  -- Referente as notas/boletim

  create index idx_falta_aluno_matricula_id on modules.falta_aluno(matricula_id);

  create index idx_falta_aluno_matricula_id_tipo on modules.falta_aluno(matricula_id,tipo_falta);

  create index idx_falta_geral_falta_aluno_id on modules.falta_geral(falta_aluno_id);

  create index idx_nota_componente_curricular_id on modules.nota_componente_curricular(componente_curricular_id);

  create index idx_nota_aluno_matricula_id on modules.nota_aluno(id, matricula_id);

  create index idx_parecer_aluno_matricula_id on modules.parecer_aluno(matricula_id);

  create index idx_parecer_geral_parecer_aluno_etp on modules.parecer_geral(parecer_aluno_id, etapa);

  create index idx_nota_componente_curricular_etp on modules.nota_componente_curricular(componente_curricular_id,etapa);

  create index idx_nota_componente_etp_aluno on modules.nota_componente_curricular(componente_curricular_id,etapa,nota_aluno_id);

  create index idx_nota_aluno_matricula on modules.nota_aluno(matricula_id);

  create index idx_falta_componente_curricular_id1 on modules.falta_componente_curricular(falta_aluno_id,componente_curricular_id,etapa);

  create index idx_serie_regra_avaliacao_id on pmieducar.serie(regra_avaliacao_id);

  create index idx_serie_cod_regra_avaliacao_id on pmieducar.serie(cod_serie, regra_avaliacao_id);

  create index idx_tabela_arredondamento_valor_tabela_id on modules.tabela_arredondamento_valor(tabela_arredondamento_id);

  -- Referente a históricos

  create index idx_historico_escolar_id1 on pmieducar.historico_escolar(ref_cod_aluno,sequencial);

  create index idx_historico_escolar_id2 on pmieducar.historico_escolar(ref_cod_aluno,sequencial, ano);

  create index idx_historico_escolar_id3 on pmieducar.historico_escolar(ref_cod_aluno, ano);

  create index idx_historico_disciplinas_id on pmieducar.historico_disciplinas(sequencial, ref_ref_cod_aluno, ref_sequencial);

  create index idx_historico_disciplinas_id1 on pmieducar.historico_disciplinas(ref_ref_cod_aluno, ref_sequencial);

  -- //@UNDO

  DROP INDEX idx_falta_aluno_matricula_id;

  DROP INDEX idx_falta_aluno_matricula_id_tipo;

  DROP INDEX idx_falta_geral_falta_aluno_id;

  DROP INDEX idx_nota_componente_curricular_id;

  DROP INDEX idx_nota_aluno_matricula_id;

  DROP INDEX idx_parecer_aluno_matricula_id;

  DROP INDEX idx_parecer_geral_parecer_aluno_etp;

  DROP INDEX idx_nota_componente_curricular_etp;

  DROP INDEX idx_nota_componente_etp_aluno;

  DROP INDEX idx_nota_aluno_matricula;

  DROP INDEX idx_falta_componente_curricular_id1;

  DROP INDEX idx_serie_regra_avaliacao_id;

  DROP INDEX idx_serie_cod_regra_avaliacao_id;

  DROP INDEX idx_tabela_arredondamento_valor_tabela_id;

  DROP INDEX idx_historico_escolar_id1;

  DROP INDEX idx_historico_escolar_id2;

  DROP INDEX idx_historico_escolar_id3;

  DROP INDEX idx_historico_disciplinas_id;

  DROP INDEX idx_historico_disciplinas_id1;

  -- //
