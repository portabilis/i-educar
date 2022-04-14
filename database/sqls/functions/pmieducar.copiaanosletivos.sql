CREATE OR REPLACE FUNCTION pmieducar.copiaanosletivos(ianonovo smallint, icodescola integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
            DECLARE
            iAnoAnterior smallint;
            BEGIN

            SELECT COALESCE(MAX(ano),0) INTO iAnoAnterior
            FROM pmieducar.escola_ano_letivo
            WHERE ref_cod_escola = iCodEscola
            AND ano < iAnoNovo;

            If iAnoAnterior IS NOT NULL THEN

                UPDATE pmieducar.escola_curso
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND NOT (iAnoNovo = ANY(anos_letivos))
                AND ref_cod_escola = iCodEscola;

                UPDATE pmieducar.escola_serie
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND NOT (iAnoNovo = ANY(anos_letivos))
                AND ref_cod_escola = iCodEscola;

                UPDATE pmieducar.escola_serie_disciplina
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND NOT (iAnoNovo = ANY(anos_letivos))
                AND ref_ref_cod_escola = iCodEscola;

                UPDATE modules.componente_curricular_ano_escolar
                SET anos_letivos = array_append(anos_letivos, iAnoNovo)
                WHERE EXISTS(
                    SELECT 1
                    FROM pmieducar.escola_serie_disciplina
                    WHERE iAnoAnterior = ANY(anos_letivos)
                    AND ref_ref_cod_escola = iCodEscola
                    AND escola_serie_disciplina.ref_cod_disciplina = componente_curricular_ano_escolar.componente_curricular_id
                    AND escola_serie_disciplina.ref_ref_cod_serie = componente_curricular_ano_escolar.ano_escolar_id
                )
                AND NOT (iAnoNovo = ANY(anos_letivos));

                INSERT INTO modules.regra_avaliacao_serie_ano
                (serie_id, regra_avaliacao_id, regra_avaliacao_diferenciada_id, ano_letivo)
                SELECT distinct serie, rasa.regra_avaliacao_id, rasa.regra_avaliacao_diferenciada_id, iAnoNovo
                FROM (
                SELECT distinct ref_cod_serie serie
                FROM pmieducar.escola_serie
                WHERE iAnoAnterior = ANY(anos_letivos)
                AND ref_cod_escola = iCodEscola
                ) AS myqq
                JOIN modules.regra_avaliacao_serie_ano rasa
                ON rasa.serie_id = serie
                AND iAnoAnterior = rasa.ano_letivo
                AND NOT EXISTS(
                    SELECT 1
                    FROM modules.regra_avaliacao_serie_ano
                    WHERE serie_id = rasa.serie_id
                    AND ano_letivo = iAnoNovo
                );

            END IF;
            END;
            $$;
