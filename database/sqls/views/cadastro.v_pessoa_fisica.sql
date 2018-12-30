CREATE OR REPLACE VIEW cadastro.v_pessoa_fisica AS
SELECT
    p.idpes,
    p.nome,
    p.url,
    p.email,
    p.situacao,
    f.nome_social,
    f.data_nasc,
    f.sexo,
    f.cpf,
    f.ref_cod_sistema,
    f.idesco,
    f.ativo
FROM cadastro.pessoa p
INNER JOIN cadastro.fisica f ON TRUE
AND f.idpes = p.idpes;
