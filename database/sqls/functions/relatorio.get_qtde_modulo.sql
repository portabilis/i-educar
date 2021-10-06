CREATE OR REPLACE FUNCTION relatorio.get_qtde_modulo(integer) RETURNS integer
    LANGUAGE sql
AS $_$
SELECT COUNT(modulo.nm_tipo)::integer AS qtde
FROM pmieducar.turma
         INNER JOIN pmieducar.curso ON (curso.cod_curso = turma.ref_cod_curso)
         LEFT JOIN pmieducar.ano_letivo_modulo ON (ano_letivo_modulo.ref_ano = turma.ano
    AND ano_letivo_modulo.ref_ref_cod_escola = turma.ref_ref_cod_escola
    AND curso.padrao_ano_escolar = 1)
         LEFT JOIN pmieducar.turma_modulo ON (turma_modulo.ref_cod_turma = turma.cod_turma
    AND curso.padrao_ano_escolar = 0)
         INNER JOIN pmieducar.modulo ON (CASE
                                             WHEN curso.padrao_ano_escolar = 1 THEN modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
                                             ELSE modulo.cod_modulo = turma_modulo.ref_cod_modulo
    END)
WHERE turma.cod_turma = $1;$_$;
