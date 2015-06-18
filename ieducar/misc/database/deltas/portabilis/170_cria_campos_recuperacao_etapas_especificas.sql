--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE SEQUENCE modules.regra_avaliacao_recuperacao_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE modules.regra_avaliacao_recuperacao
(
  id integer NOT NULL DEFAULT nextval('regra_avaliacao_recuperacao_id_seq'::regclass),
  regra_avaliacao_id integer NOT NULL,
  descricao character varying(25) NOT NULL,
  etapas_recuperadas character varying(25) NOT NULL,
  substitui_menor_nota BOOLEAN,
  media numeric(8,4) NOT NULL,
  nota_maxima numeric(8,4) NOT NULL,
  CONSTRAINT regra_avaliacao_recuperacao_pkey PRIMARY KEY (id),
  CONSTRAINT regra_avaliacao_regra_avaliacao_recuperacao_fk FOREIGN KEY (regra_avaliacao_id)
      REFERENCES modules.regra_avaliacao (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);

ALTER TABLE modules.nota_componente_curricular ADD COLUMN nota_recuperacao_especifica character varying(10);