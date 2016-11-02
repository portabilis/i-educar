 	-- //

 	--
 	-- Remove obrigatoriedade campos enderecamento na tabela editora.
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN cep DROP NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN ref_sigla_uf DROP NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN cidade DROP NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN bairro DROP NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN ref_idtlog DROP NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN logradouro DROP NOT NULL;

	-- //@UNDO

  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN cep SET NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN ref_sigla_uf SET NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN cidade SET NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN bairro SET NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN ref_idtlog SET NOT NULL;
  ALTER TABLE pmieducar.acervo_editora  ALTER COLUMN logradouro SET NOT NULL;

	-- //
