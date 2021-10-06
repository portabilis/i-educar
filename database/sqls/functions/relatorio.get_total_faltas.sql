CREATE OR REPLACE FUNCTION relatorio.get_total_faltas(matricula_i integer) RETURNS numeric
    LANGUAGE plpgsql
AS $$
BEGIN
    RETURN (SELECT sum(falta_geral.quantidade)
            FROM modules.falta_geral,
                 modules.falta_aluno
            WHERE falta_geral.falta_aluno_id = falta_aluno.id AND
                    falta_aluno.matricula_id = matricula_i AND
                    falta_aluno.tipo_falta = 1 AND
                    falta_geral.etapa in ('1','2','3','4'));
END; $$;
