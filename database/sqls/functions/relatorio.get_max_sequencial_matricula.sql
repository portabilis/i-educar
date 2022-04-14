CREATE OR REPLACE FUNCTION relatorio.get_max_sequencial_matricula(integer) RETURNS integer
    LANGUAGE sql
AS $_$
SELECT MAX(matricula_turma.sequencial)
FROM pmieducar.matricula_turma
         INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
         INNER JOIN relatorio.view_situacao ON (view_situacao.cod_matricula = matricula.cod_matricula
    AND view_situacao.cod_turma = matricula_turma.ref_cod_turma
    AND view_situacao.sequencial = matricula_turma.sequencial)
WHERE ref_cod_matricula = $1;
$_$;
