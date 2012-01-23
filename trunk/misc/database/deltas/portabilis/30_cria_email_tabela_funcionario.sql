 	-- //
  
 	--
 	-- Adiciona campo email, na tabela funcionário para ser utilizado na recuperação de senha. 
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  

  ALTER TABLE portal.funcionario ADD COLUMN email character varying(50);
  ALTER TABLE portal.funcionario ADD COLUMN status_token character varying(50);

  DROP VIEW portal.v_funcionario;

  CREATE OR REPLACE VIEW portal.v_funcionario AS 
  SELECT f.ref_cod_pessoa_fj, f.matricula, f.senha, f.ativo, f.ramal, f.sequencial, f.opcao_menu, f.ref_cod_setor, f.ref_cod_funcionario_vinculo, f.tempo_expira_senha, f.tempo_expira_conta, f.data_troca_senha, f.data_reativa_conta, f.ref_ref_cod_pessoa_fj, f.proibido, f.ref_cod_setor_new, f.email, (SELECT pessoa.nome FROM pessoa WHERE pessoa.idpes = f.ref_cod_pessoa_fj::numeric) AS nome FROM funcionario f;

  -- após recriar a view caso ocorra erros de permissões ao acessa-la, conceder permissões com o comando:
  -- GRANT ALL ON TABLE portal.v_funcionario TO <username>;


	-- //@UNDO

  ALTER TABLE portal.funcionario DROP COLUMN email;
  ALTER TABLE portal.funcionario DROP COLUMN status_token;

  DROP VIEW portal.v_funcionario;

  CREATE OR REPLACE VIEW portal.v_funcionario AS 
  SELECT f.ref_cod_pessoa_fj, f.matricula, f.senha, f.ativo, f.ramal, f.sequencial, f.opcao_menu, f.ref_cod_setor, f.ref_cod_funcionario_vinculo, f.tempo_expira_senha, f.tempo_expira_conta, f.data_troca_senha, f.data_reativa_conta, f.ref_ref_cod_pessoa_fj, f.proibido, f.ref_cod_setor_new, (SELECT pessoa.nome FROM pessoa WHERE pessoa.idpes = f.ref_cod_pessoa_fj::numeric) AS nome FROM funcionario f;

  -- após recriar a view caso ocorra erros de permissões ao acessa-la, conceder permissões com o comando:
  -- GRANT ALL ON TABLE portal.v_funcionario TO <username>;

	-- //
