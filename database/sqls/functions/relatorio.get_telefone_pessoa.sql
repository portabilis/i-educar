CREATE OR REPLACE FUNCTION relatorio.get_telefone_pessoa(idPessoa NUMERIC)
    RETURNS CHARACTER VARYING
AS $$
DECLARE
    telefones RECORD;
BEGIN
    SELECT fone1.ddd ddd1,
           fone1.fone fone1,
           fone2.ddd ddd2,
           fone2.fone fone2,
           fone3.ddd ddd3,
           fone3.fone fone3,
           fone3.ddd ddd4,
           fone3.fone fone4
    FROM cadastro.pessoa
             LEFT JOIN cadastro.fone_pessoa AS fone1 ON fone1.idpes = pessoa.idpes AND fone1.tipo = 1
             LEFT JOIN cadastro.fone_pessoa AS fone2 ON fone2.idpes = pessoa.idpes AND fone2.tipo = 2
             LEFT JOIN cadastro.fone_pessoa AS fone3 ON fone3.idpes = pessoa.idpes AND fone3.tipo = 3
             LEFT JOIN cadastro.fone_pessoa AS fone4 ON fone4.idpes = pessoa.idpes AND fone4.tipo = 4
    WHERE pessoa.idpes = idPessoa INTO telefones;

    CASE
        WHEN
            telefones.fone2 IS NOT NULL THEN
            RETURN '(' || telefones.ddd2 || ') ' || telefones.fone2;
        WHEN
            telefones.fone1 IS NOT NULL THEN
            RETURN '(' || telefones.ddd1 || ') ' || telefones.fone1;
        WHEN
            telefones.fone3 IS NOT NULL THEN
            RETURN '(' || telefones.ddd3 || ') ' || telefones.fone3;
        WHEN
            telefones.fone4 IS NOT NULL THEN
            RETURN '(' || telefones.ddd4 || ') ' || telefones.fone4;
        ELSE
            RETURN null;
        END CASE;

END
$$
LANGUAGE plpgsql;
