-- Cria tabela de dispensa etapa
-- @author Paula Bonot <bonot@portabilis.com.br>

CREATE TABLE pmieducar.dispensa_etapa
(
  ref_cod_dispensa integer,
  etapa integer,
  CONSTRAINT ref_cod_disciplina FOREIGN KEY (ref_cod_dispensa)
      REFERENCES pmieducar.dispensa_disciplina (cod_dispensa) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

ALTER TABLE pmieducar.dispensa_etapa
  OWNER TO ieducar;

--UNDO

DROP TABLE pmieducar.dispensa_etapa;