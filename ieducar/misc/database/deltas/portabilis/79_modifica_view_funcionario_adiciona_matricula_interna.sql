  -- //

  --
  -- Adiciona coluna em portal.funcionario para registro de matrícula interna
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  DROP VIEW portal.v_funcionario;

  CREATE OR REPLACE VIEW portal.v_funcionario AS 
   SELECT f.ref_cod_pessoa_fj, f.matricula, f.matricula_interna, f.senha, f.ativo, f.ramal, f.sequencial, f.opcao_menu, f.ref_cod_setor, f.ref_cod_funcionario_vinculo, f.tempo_expira_senha, f.tempo_expira_conta, f.data_troca_senha, f.data_reativa_conta, f.ref_ref_cod_pessoa_fj, f.proibido, f.ref_cod_setor_new, f.email, ( SELECT pessoa.nome
             FROM pessoa
            WHERE pessoa.idpes = f.ref_cod_pessoa_fj::numeric) AS nome
     FROM funcionario f;

  ALTER TABLE portal.v_funcionario
  OWNER TO postgres;

  -- //@UNDO

  DROP VIEW portal.v_funcionario;

  CREATE OR REPLACE VIEW portal.v_funcionario AS 
   SELECT f.ref_cod_pessoa_fj, f.matricula, f.senha, f.ativo, f.ramal, f.sequencial, f.opcao_menu, f.ref_cod_setor, f.ref_cod_funcionario_vinculo, f.tempo_expira_senha, f.tempo_expira_conta, f.data_troca_senha, f.data_reativa_conta, f.ref_ref_cod_pessoa_fj, f.proibido, f.ref_cod_setor_new, f.email, ( SELECT pessoa.nome
             FROM pessoa
            WHERE pessoa.idpes = f.ref_cod_pessoa_fj::numeric) AS nome
     FROM funcionario f;

  ALTER TABLE portal.v_funcionario
    OWNER TO postgres;
  
  -- //
