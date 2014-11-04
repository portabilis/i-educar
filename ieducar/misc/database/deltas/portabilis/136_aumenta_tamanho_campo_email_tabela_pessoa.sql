  --
  -- Aumenta tamanho do campo email no cadastro de pessoa
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

DROP VIEW cadastro.v_pessoa_fisica;

ALTER TABLE cadastro.pessoa ALTER COLUMN email type varchar(100);

CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS
 SELECT p.idpes, p.nome, p.url, p.email, p.situacao, f.data_nasc, f.sexo, f.cpf, f.ref_cod_sistema, f.idesco
   FROM pessoa p, fisica f
  WHERE p.idpes = f.idpes;

ALTER TABLE cadastro.v_pessoa_fisica
  OWNER TO ieducar;