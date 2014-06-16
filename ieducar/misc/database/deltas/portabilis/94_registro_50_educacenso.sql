  -- //

  --
  -- Cria colunas necessárias para atender o registro 50 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE cadastro.escolaridade ADD COLUMN escolaridade SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN situacao_curso_superior_1 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN formacao_complementacao_pedagogica_1 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN codigo_curso_superior_1 INTEGER;

  ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_1_fk FOREIGN KEY (codigo_curso_superior_1)
  REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE;

  ALTER TABLE pmieducar.servidor ADD COLUMN ano_inicio_curso_superior_1 NUMERIC(4,0);

  ALTER TABLE pmieducar.servidor ADD COLUMN ano_conclusao_curso_superior_1 NUMERIC(4,0);

  ALTER TABLE pmieducar.servidor ADD COLUMN tipo_instituicao_curso_superior_1 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN instituicao_curso_superior_1 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD CONSTRAINT instituicao_curso_superior_1_fk FOREIGN KEY (instituicao_curso_superior_1)
  REFERENCES modules.educacenso_ies (id) MATCH SIMPLE;

  ALTER TABLE pmieducar.servidor ADD COLUMN situacao_curso_superior_2 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN formacao_complementacao_pedagogica_2 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN codigo_curso_superior_2 INTEGER;

  ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_2_fk FOREIGN KEY (codigo_curso_superior_2)
  REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE;

  ALTER TABLE pmieducar.servidor ADD COLUMN ano_inicio_curso_superior_2 NUMERIC(4,0);

  ALTER TABLE pmieducar.servidor ADD COLUMN ano_conclusao_curso_superior_2 NUMERIC(4,0);

  ALTER TABLE pmieducar.servidor ADD COLUMN tipo_instituicao_curso_superior_2 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN instituicao_curso_superior_2 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD CONSTRAINT instituicao_curso_superior_2_fk FOREIGN KEY (instituicao_curso_superior_2)
  REFERENCES modules.educacenso_ies (id) MATCH SIMPLE;

  ALTER TABLE pmieducar.servidor ADD COLUMN situacao_curso_superior_3 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN formacao_complementacao_pedagogica_3 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN codigo_curso_superior_3 INTEGER;

  ALTER TABLE pmieducar.servidor ADD CONSTRAINT codigo_curso_superior_3_fk FOREIGN KEY (codigo_curso_superior_3)
  REFERENCES modules.educacenso_curso_superior (id) MATCH SIMPLE;

  ALTER TABLE pmieducar.servidor ADD COLUMN ano_inicio_curso_superior_3 NUMERIC(4,0);

  ALTER TABLE pmieducar.servidor ADD COLUMN ano_conclusao_curso_superior_3 NUMERIC(4,0);

  ALTER TABLE pmieducar.servidor ADD COLUMN tipo_instituicao_curso_superior_3 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN instituicao_curso_superior_3 SMALLINT;

  ALTER TABLE pmieducar.servidor ADD CONSTRAINT instituicao_curso_superior_3_fk FOREIGN KEY (instituicao_curso_superior_3)
  REFERENCES modules.educacenso_ies (id) MATCH SIMPLE;    

  ALTER TABLE pmieducar.servidor ADD COLUMN pos_especializacao SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN pos_mestrado SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN pos_doutorado SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN pos_nenhuma SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_creche SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_pre_escola SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_anos_iniciais SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_anos_finais SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_ensino_medio SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_eja SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_especial SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_indigena SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_campo SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_ambiental SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_educacao_direitos_humanos SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_genero_diversidade_sexual SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_direito_crianca_adolescente SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_relacoes_etnicorraciais SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_outros SMALLINT;

  ALTER TABLE pmieducar.servidor ADD COLUMN curso_nenhum SMALLINT;


  -- //@UNDO

  ALTER TABLE cadastro.escolaridade DROP COLUMN escolaridade;

  ALTER TABLE pmieducar.servidor DROP COLUMN situacao_curso_superior_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN formacao_complementacao_pedagogica_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN codigo_curso_superior_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN ano_inicio_curso_superior_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN ano_conclusao_curso_superior_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN tipo_instituicao_curso_superior_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN instituicao_curso_superior_1;

  ALTER TABLE pmieducar.servidor DROP COLUMN situacao_curso_superior_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN formacao_complementacao_pedagogica_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN codigo_curso_superior_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN ano_inicio_curso_superior_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN ano_conclusao_curso_superior_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN tipo_instituicao_curso_superior_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN instituicao_curso_superior_2;

  ALTER TABLE pmieducar.servidor DROP COLUMN situacao_curso_superior_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN formacao_complementacao_pedagogica_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN codigo_curso_superior_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN ano_inicio_curso_superior_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN ano_conclusao_curso_superior_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN tipo_instituicao_curso_superior_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN instituicao_curso_superior_3;

  ALTER TABLE pmieducar.servidor DROP COLUMN pos_especializacao;

  ALTER TABLE pmieducar.servidor DROP COLUMN pos_mestrado;

  ALTER TABLE pmieducar.servidor DROP COLUMN pos_doutorado;

  ALTER TABLE pmieducar.servidor DROP COLUMN pos_nenhuma;

  ALTER TABLE pmieducar.servidor DROP COLUMN curso_creche;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_pre_escola;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_anos_iniciais;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_anos_finais;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_ensino_medio;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_eja;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_especial;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_indigena;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_campo;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_ambiental;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_educacao_direitos_humanos;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_genero_diversidade_sexual;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_direito_crianca_adolescente;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_relacoes_etnicorraciais;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_outros;
  
  ALTER TABLE pmieducar.servidor DROP COLUMN curso_nenhum;
  -- //