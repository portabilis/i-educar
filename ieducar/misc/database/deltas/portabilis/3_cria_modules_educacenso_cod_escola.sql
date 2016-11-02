-- //

--
-- Cria as tabelas modules.educacenso_cod_escola
--
-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE modules.educacenso_cod_escola
(
  cod_escola integer NOT NULL,
  cod_escola_inep bigint NOT NULL,
  nome_inep character varying(255),
  fonte character varying(255),
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone,
  CONSTRAINT educacenso_cod_escola_pk PRIMARY KEY (cod_escola, cod_escola_inep),
  CONSTRAINT educacenso_cod_escola_cod_escola_fk FOREIGN KEY (cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);

-- //@UNDO

DROP TABLE modules.educacenso_cod_escola;

-- //