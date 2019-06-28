CREATE OR REPLACE FUNCTION modules.frequencia_matricula_por_etapa(matricula integer, etapa character varying) RETURNS numeric
    LANGUAGE plpgsql
    AS $_$
                          DECLARE
                            matricula integer;
                            etapa varchar;
                            ano_matricula integer;
                            escola_matricula integer;
                            turma integer;
                            faltas_geral_matricula decimal;
                            var_dias_letivos_turma_etapa decimal;
                            dias_letivos_escola_etapa decimal;

                            BEGIN
                              matricula := $1;
                              etapa := $2;
                              turma := (SELECT ref_cod_turma
                                          FROM pmieducar.matricula_turma
                                         WHERE ref_cod_matricula = $1
                                            AND matricula_turma.sequencial = relatorio.get_max_sequencial_matricula(ref_cod_matricula));
                              ano_matricula := (SELECT ano
                                                  FROM pmieducar.matricula
                                                 WHERE cod_matricula = $1);
                              escola_matricula := (SELECT ref_ref_cod_escola
                                                     FROM pmieducar.matricula
                                                    WHERE cod_matricula = $1);
                              faltas_geral_matricula := (SELECT sum(falta_geral.quantidade)
                                                               FROM modules.falta_geral,
                                                                    modules.falta_aluno
                                                              WHERE falta_geral.falta_aluno_id = falta_aluno.id
                                                                AND falta_aluno.matricula_id = $1
                                                                AND falta_geral.etapa = $2
                                                                AND falta_aluno.tipo_falta = 1);
                              var_dias_letivos_turma_etapa := (SELECT dias_letivos
                                                                 FROM pmieducar.turma_modulo
                                                                WHERE sequencial::varchar = $2
                                                                  AND ref_cod_turma = turma);
                              dias_letivos_escola_etapa := (SELECT dias_letivos
                                                                  FROM pmieducar.ano_letivo_modulo
                                                                 WHERE sequencial::varchar = $2
                                                                   AND ref_ano = ano_matricula
                                                                   AND ref_ref_cod_escola = escola_matricula);
                              IF (var_dias_letivos_turma_etapa IS NOT NULL AND var_dias_letivos_turma_etapa <> 0) THEN
                                RETURN ((var_dias_letivos_turma_etapa - faltas_geral_matricula) * 100) / var_dias_letivos_turma_etapa;
                              ELSE
                                IF (dias_letivos_escola_etapa IS NOT NULL AND dias_letivos_escola_etapa <> 0) THEN
                                  RETURN ((var_dias_letivos_turma_etapa - faltas_geral_matricula) * 100) / var_dias_letivos_turma_etapa;
                                END IF;
                              END IF;
                            END;
                          $_$;
