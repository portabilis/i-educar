  -- //

  -- Cria colunas necessárias para atender o registro 20 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
  
  ALTER TABLE pmieducar.turma ADD COLUMN tipo_atendimento SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN turma_mais_educacao SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN atividade_complementar_1 INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN atividade_complementar_2 INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN atividade_complementar_3 INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN atividade_complementar_4 INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN atividade_complementar_5 INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN atividade_complementar_6 INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_braille SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_recurso_optico SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_estrategia_desenvolvimento SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_tecnica_mobilidade SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_libras SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_caa SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_curricular SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_soroban SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_informatica SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_lingua_escrita SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN aee_autonomia SMALLINT;
  ALTER TABLE pmieducar.turma ADD COLUMN etapa_id INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN cod_curso_profissional INTEGER;
  ALTER TABLE pmieducar.turma ADD COLUMN turma_sem_professor SMALLINT;

  ALTER TABLE pmieducar.turma ADD CONSTRAINT turma_etapa_id_fk FOREIGN KEY (etapa_id)
  REFERENCES modules.etapas_educacenso (id) MATCH SIMPLE;

  ALTER TABLE modules.componente_curricular ADD COLUMN codigo_educacenso SMALLINT;
  ALTER TABLE modules.componente_curricular_turma ADD COLUMN docente_vinculado SMALLINT;

  -- //@UNDO
  
  ALTER TABLE pmieducar.turma DROP COLUMN tipo_atendimento;
  ALTER TABLE pmieducar.turma DROP COLUMN turma_mais_educacao;
  ALTER TABLE pmieducar.turma DROP COLUMN atividade_complementar_1;
  ALTER TABLE pmieducar.turma DROP COLUMN atividade_complementar_2;
  ALTER TABLE pmieducar.turma DROP COLUMN atividade_complementar_3;
  ALTER TABLE pmieducar.turma DROP COLUMN atividade_complementar_4;
  ALTER TABLE pmieducar.turma DROP COLUMN atividade_complementar_5;
  ALTER TABLE pmieducar.turma DROP COLUMN atividade_complementar_6;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_braille;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_recurso_optico;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_estrategia_desenvolvimento;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_tecnica_mobilidade;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_libras;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_caa;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_curricular;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_soroban;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_informatica;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_lingua_escrita;
  ALTER TABLE pmieducar.turma DROP COLUMN aee_autonomia;
  ALTER TABLE pmieducar.turma DROP COLUMN etapa_id;
  ALTER TABLE pmieducar.turma DROP COLUMN cod_curso_profissional;
  ALTER TABLE pmieducar.turma DROP COLUMN turma_sem_professor;

  ALTER TABLE modules.componente_curricular DROP COLUMN codigo_educacenso;

  ALTER TABLE modules.componente_curricular_turma DROP COLUMN docente_vinculado;

  -- //