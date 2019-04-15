CREATE OR REPLACE VIEW portal.v_funcionario AS
SELECT
    f.ref_cod_pessoa_fj,
    f.matricula,
    f.matricula_interna,
    f.senha,
    f.ativo,
    f.ramal,
    f.sequencial,
    f.opcao_menu,
    f.ref_cod_setor,
    f.ref_cod_funcionario_vinculo,
    f.tempo_expira_senha,
    f.tempo_expira_conta,
    f.data_troca_senha,
    f.data_reativa_conta,
    f.ref_ref_cod_pessoa_fj,
    f.proibido,
    f.ref_cod_setor_new,
    f.email,
    (
        SELECT pessoa.nome
        FROM cadastro.pessoa
        WHERE pessoa.idpes = f.ref_cod_pessoa_fj::numeric
    ) AS nome
FROM portal.funcionario f;
