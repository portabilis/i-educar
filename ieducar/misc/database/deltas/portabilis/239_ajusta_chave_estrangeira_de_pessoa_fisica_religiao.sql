--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE cadastro.fisica DROP CONSTRAINT fisica_ref_cod_religiao;
ALTER TABLE cadastro.fisica ADD CONSTRAINT fisica_ref_cod_religiao FOREIGN KEY (ref_cod_religiao)
      REFERENCES pmieducar.religiao (cod_religiao) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

-- undo

ALTER TABLE cadastro.fisica DROP CONSTRAINT fisica_ref_cod_religiao;
ALTER TABLE cadastro.fisica ADD CONSTRAINT fisica_ref_cod_religiao FOREIGN KEY (ref_cod_religiao)
      REFERENCES cadastro.religiao (cod_religiao) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;