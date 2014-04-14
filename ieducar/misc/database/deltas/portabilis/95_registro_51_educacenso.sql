  -- //

  --
  -- Cria colunas necessárias para atender o registro 51 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE SEQUENCE modules.professor_turma_id_seq
    INCREMENT 1
    MINVALUE 0
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;

  CREATE TABLE modules.professor_turma
  (
    id INTEGER NOT NULL DEFAULT nextval('professor_turma_id_seq'::regclass),
    ano SMALLINT NOT NULL,
    instituicao_id INTEGER NOT NULL,
    turma_id INTEGER NOT NULL,
    servidor_id INTEGER NOT NULL,    
    funcao_exercida SMALLINT NOT NULL,
    tipo_vinculo SMALLINT,    
    CONSTRAINT professor_turma_id_pk PRIMARY KEY (id ),
    CONSTRAINT professor_turma_turma_id_fk FOREIGN KEY (turma_id)
        REFERENCES pmieducar.turma (cod_turma) MATCH SIMPLE
        ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT professor_turma_servidor_id_fk FOREIGN KEY (servidor_id, instituicao_id)
        REFERENCES pmieducar.servidor (cod_servidor, ref_cod_instituicao) MATCH FULL
        ON UPDATE RESTRICT ON DELETE RESTRICT
  )
  WITH (
    OIDS=FALSE
  );

  CREATE TABLE modules.professor_turma_disciplina
  (
    professor_turma_id INTEGER NOT NULL,
    componente_curricular_id INTEGER NOT NULL,
    CONSTRAINT professor_turma_disciplina_pk PRIMARY KEY (professor_turma_id, componente_curricular_id),
    CONSTRAINT professor_turma_disciplina_professor_turma_id_fk FOREIGN KEY (professor_turma_id)
        REFERENCES modules.professor_turma (id) MATCH FULL
        ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT professor_turma_disciplina_componente_curricular_id_fk FOREIGN KEY (componente_curricular_id)
        REFERENCES modules.componente_curricular (id) MATCH FULL
        ON UPDATE RESTRICT ON DELETE RESTRICT
  )
  WITH (
    OIDS=FALSE
  );

  -- //@UNDO

  DROP TABLE modules.professor_turma_disciplina;

  DROP TABLE modules.professor_turma;

  DROP SEQUENCE modules.professor_turma_id_seq;
  
  -- //