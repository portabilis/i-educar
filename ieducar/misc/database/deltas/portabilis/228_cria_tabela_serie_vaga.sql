--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE TABLE pmieducar.serie_vaga
(
  ano integer NOT NULL,
  cod_serie_vaga integer NOT NULL,
  ref_cod_instituicao integer NOT NULL,
  ref_cod_escola integer NOT NULL,
  ref_cod_curso integer NOT NULL,
  ref_cod_serie integer NOT NULL,
  vagas SMALLINT NOT NULL,
  CONSTRAINT cod_serie_vaga_pkey PRIMARY KEY (cod_serie_vaga),
  CONSTRAINT serie_vaga_ref_cod_instituicao_fk FOREIGN KEY (ref_cod_instituicao)
      REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT serie_vaga_ref_cod_escola_fk FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT serie_vaga_ref_cod_serie_fk FOREIGN KEY (ref_cod_serie)
      REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT serie_vaga_ref_cod_curso_fk FOREIGN KEY (ref_cod_curso)
      REFERENCES pmieducar.curso (cod_CURSO) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT cod_serie_vaga_unique UNIQUE (ano, ref_cod_instituicao, ref_cod_escola, ref_cod_curso, ref_cod_serie)
)
WITH (
  OIDS=FALSE
);

INSERT INTO portal.menu_submenu (cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, title, nivel) VALUES (21253, 55, 2, 'Vagas por série/ano', 'educar_serie_vaga_lst.php', '', 3);
INSERT INTO pmicontrolesis.menu VALUES (21253, 21253, 21122, 'Vagas por série/ano', 2, 'educar_serie_vaga_lst.php', '_self', 1, 15, 1);