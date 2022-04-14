CREATE OR REPLACE VIEW relatorio.view_modulo AS
SELECT DISTINCT turma.cod_turma,
                modulo_curso.cod_modulo AS cod_modulo_curso,
                modulo_turma.cod_modulo AS cod_modulo_turma,
                CASE
                    WHEN ((curso.padrao_ano_escolar = 0) AND (modulo_turma.cod_modulo IS NOT NULL)) THEN modulo_turma.nm_tipo
                    ELSE modulo_curso.nm_tipo
                    END AS nome,
                CASE
                    WHEN ((curso.padrao_ano_escolar = 0) AND (modulo_turma.cod_modulo IS NOT NULL)) THEN turma_modulo.sequencial
                    ELSE ano_letivo_modulo.sequencial
                    END AS sequencial
FROM (((((pmieducar.turma
    JOIN pmieducar.curso ON ((curso.cod_curso = turma.ref_cod_curso)))
    LEFT JOIN pmieducar.ano_letivo_modulo ON (((ano_letivo_modulo.ref_ano = turma.ano) AND (ano_letivo_modulo.ref_ref_cod_escola = turma.ref_ref_cod_escola))))
    LEFT JOIN pmieducar.turma_modulo ON ((turma_modulo.ref_cod_turma = turma.cod_turma)))
    LEFT JOIN pmieducar.modulo modulo_curso ON ((modulo_curso.cod_modulo = ano_letivo_modulo.ref_cod_modulo)))
         LEFT JOIN pmieducar.modulo modulo_turma ON ((modulo_turma.cod_modulo = turma_modulo.ref_cod_modulo)))
ORDER BY turma.cod_turma, modulo_curso.cod_modulo, modulo_turma.cod_modulo,
         CASE
             WHEN ((curso.padrao_ano_escolar = 0) AND (modulo_turma.cod_modulo IS NOT NULL)) THEN modulo_turma.nm_tipo
             ELSE modulo_curso.nm_tipo
             END,
         CASE
             WHEN ((curso.padrao_ano_escolar = 0) AND (modulo_turma.cod_modulo IS NOT NULL)) THEN turma_modulo.sequencial
             ELSE ano_letivo_modulo.sequencial
             END;
