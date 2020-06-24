CREATE OR REPLACE FUNCTION relatorio.get_telefone_escola(integer) RETURNS character varying
    LANGUAGE sql
AS $_$
SELECT COALESCE(
           (SELECT min(to_char(fone_pessoa.fone, '99999-9999'))
            FROM cadastro.fone_pessoa, cadastro.juridica
            WHERE juridica.idpes = fone_pessoa.idpes
              AND juridica.idpes =
                  (SELECT idpes
                   FROM cadastro.pessoa
                            INNER JOIN pmieducar.escola ON escola.ref_idpes = pessoa.idpes
                   WHERE cod_escola = $1)),
           (SELECT min(to_char(telefone, '99999-9999'))
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = $1)); $_$;

