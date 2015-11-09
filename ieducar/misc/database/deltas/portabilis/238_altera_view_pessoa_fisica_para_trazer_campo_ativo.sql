--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

DROP VIEW cadastro.v_pessoa_fisica;

CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS 
 SELECT p.idpes, p.nome, p.url, p.email, p.situacao, f.data_nasc, f.sexo, f.cpf, f.ref_cod_sistema, f.idesco, f.ativo
   FROM cadastro.pessoa p, cadastro.fisica f
  WHERE p.idpes = f.idpes;

ALTER TABLE cadastro.v_pessoa_fisica
  OWNER TO ieducar;

-- undo

DROP VIEW cadastro.v_pessoa_fisica;

CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS 
 SELECT p.idpes, p.nome, p.url, p.email, p.situacao, f.data_nasc, f.sexo, f.cpf, f.ref_cod_sistema, f.idesco
   FROM cadastro.pessoa p, cadastro.fisica f
  WHERE p.idpes = f.idpes;

ALTER TABLE cadastro.v_pessoa_fisica
  OWNER TO ieducar;