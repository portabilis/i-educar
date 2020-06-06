CREATE OR REPLACE FUNCTION relatorio.get_ultima_matricula_turma(integer, integer, integer) RETURNS boolean
    LANGUAGE plpgsql
AS $_$
DECLARE
    max_matricula integer;
    cod_aluno integer;
BEGIN
    cod_aluno := (SELECT ref_cod_aluno FROM pmieducar.matricula WHERE cod_matricula = $1);
    max_matricula := (SELECT max(matricula.cod_matricula)
                      FROM pmieducar.matricula
                               INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
                      WHERE ref_cod_aluno = cod_aluno
                        AND ref_cod_turma = $2
                        AND matricula.aprovado = $3);
    IF max_matricula = $1 THEN
        RETURN true;
    END IF;
    RETURN FALSE;
END;
$_$;
