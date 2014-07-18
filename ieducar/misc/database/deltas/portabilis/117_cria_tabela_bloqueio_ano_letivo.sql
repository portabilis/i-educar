  -- //
  -- Essa migração cria tabela para bloqueio do ano letivo
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE TABLE pmieducar.bloqueio_ano_letivo
  (
    ref_cod_instituicao INTEGER NOT NULL,
    ref_ano INTEGER NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    CONSTRAINT pmieducar_bloqueio_ano_letivo_pkey PRIMARY KEY (ref_cod_instituicao, ref_ano),
    CONSTRAINT pmieducar_bloqueio_ano_letivo_ref_cod_instituicao FOREIGN KEY (ref_cod_instituicao)
    REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE
  )
  WITH (
    OIDS=TRUE
  );

  INSERT INTO portal.menu_submenu (cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, title, nivel) VALUES (21251, 55, 2, 'Bloqueio do ano letivo', 'educar_bloqueio_ano_letivo_lst.php', '', 3);

  INSERT INTO pmicontrolesis.menu VALUES (21251, 21251, 21122, 'Bloqueio do ano letivo', 2, 'educar_bloqueio_ano_letivo_lst.php', '_self', 1, 15, 1);

  -- //@UNDO

  DROP TABLE pmieducar.bloqueio_ano_letivo;

  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 21251;

  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 21251;

  -- //