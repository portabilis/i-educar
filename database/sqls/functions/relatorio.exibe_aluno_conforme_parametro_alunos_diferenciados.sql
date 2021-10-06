CREATE OR REPLACE FUNCTION relatorio.exibe_aluno_conforme_parametro_alunos_diferenciados(
    codigo_aluno integer,
    alunos_diferenciados integer)
    RETURNS boolean AS
$BODY$
DECLARE
    possui_deficiencia boolean;
BEGIN

    possui_deficiencia := EXISTS
        (SELECT 1
         FROM cadastro.fisica_deficiencia fd
                  JOIN pmieducar.aluno a ON fd.ref_idpes = a.ref_idpes
                  JOIN cadastro.deficiencia d ON d.cod_deficiencia = fd.ref_cod_deficiencia
         WHERE a.cod_aluno = codigo_aluno
           AND d.desconsidera_regra_diferenciada = false
         LIMIT 1
        );

    CASE alunos_diferenciados
        WHEN 1 THEN RETURN possui_deficiencia = false;
        WHEN 2 THEN RETURN possui_deficiencia = true;
        ELSE RETURN true;
        END CASE;

END; $BODY$
    LANGUAGE plpgsql VOLATILE
                     COST 100;
