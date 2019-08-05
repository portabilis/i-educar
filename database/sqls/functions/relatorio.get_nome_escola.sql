CREATE OR REPLACE FUNCTION relatorio.get_nome_escola(integer) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT COALESCE(
               (SELECT COALESCE (fcn_upper(ps.nome),fcn_upper(juridica.fantasia))
            FROM cadastro.pessoa ps, cadastro.juridica
           WHERE escola.ref_idpes = juridica.idpes
             AND juridica.idpes = ps.idpes
             AND ps.idpes = escola.ref_idpes),
               (SELECT nm_escola
            FROM pmieducar.escola_complemento
           WHERE ref_cod_escola = escola.cod_escola))
          FROM pmieducar.escola
         WHERE escola.cod_escola = $1;$_$;
