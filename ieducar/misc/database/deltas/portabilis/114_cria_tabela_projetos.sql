  -- //
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE SEQUENCE pmieducar.projeto_seq
    INCREMENT 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
    CACHE 1;

  CREATE TABLE pmieducar.projeto
  (
    cod_projeto integer NOT NULL DEFAULT nextval('pmieducar.projeto_seq'::regclass),
    nome character varying(50),
    observacao character varying(255),
    CONSTRAINT pmieducar_projeto_cod_projeto PRIMARY KEY (cod_projeto)
  )
  WITH (
    OIDS=TRUE
  );

  INSERT INTO portal.menu_submenu (cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, title, nivel) VALUES (21250, 55, 2, 'Projetos', 'educar_projeto_lst.php', '', 3);

  INSERT INTO pmicontrolesis.menu VALUES (21250, 21250, 21171, 'Projetos', 2, 'educar_projeto_lst.php', '_self', 1, 15, 1);

  -- //@UNDO

  DROP TABLE pmieducar.projeto;

  DROP SEQUENCE pmieducar.projeto_seq;

  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 21250;

  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 21250;

  -- //