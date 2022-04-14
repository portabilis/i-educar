CREATE OR REPLACE FUNCTION relatorio.get_ddd_escola(integer) RETURNS numeric
    LANGUAGE sql
AS $_$
SELECT COALESCE(
           (SELECT min(fone_pessoa.ddd)
            FROM cadastro.fone_pessoa, cadastro.juridica
            WHERE juridica.idpes = fone_pessoa.idpes
              AND juridica.idpes =
                  (SELECT idpes
                   FROM cadastro.pessoa
                            INNER JOIN pmieducar.escola ON escola.ref_idpes = pessoa.idpes
                   WHERE cod_escola = $1)),
           (SELECT min(ddd_telefone)
            FROM pmieducar.escola_complemento
            WHERE ref_cod_escola = $1)); $_$;
