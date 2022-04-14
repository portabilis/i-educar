CREATE OR REPLACE FUNCTION relatorio.get_total_geral_falta_componente(matricula_i integer) RETURNS numeric
    LANGUAGE plpgsql
AS $$
BEGIN
    RETURN (SELECT sum(falta_componente_curricular.quantidade)
            FROM modules.falta_componente_curricular,
                 modules.falta_aluno
            WHERE falta_componente_curricular.falta_aluno_id = falta_aluno.id AND
                    falta_aluno.matricula_id = matricula_i AND
                    falta_componente_curricular.etapa in ('1','2','3','4') AND
                    falta_aluno.tipo_falta = 2);
END; $$;
