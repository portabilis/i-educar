<?php

class EtapasController extends ApiCoreController
{
    protected function getEtapasEspecificas()
    {
        if (empty($this->validatesPresenceOf(['instituicao_id', 'escola', 'ano']))) {
            return [];
        }

        $instituicaoId = $this->getRequest()->instituicao_id;
        $ano = $this->getRequest()->ano;
        $escola = $this->getRequest()->escola;
        $modified = $this->getRequest()->modified;

        $params = [$instituicaoId, $ano];

        if (is_array($escola)) {
            $escola = implode(',', $escola);
        }

        $whereEscolaSerieDisciplina = '';
        $whereComponenteCurricularTurma = '';

        if ($modified) {
            $params[] = $modified;
            $whereEscolaSerieDisciplina = ' AND esd.updated_at >= $3';
            $whereComponenteCurricularTurma = ' AND cct.updated_at >= $3';
        }

        $sql = "
            (
                SELECT
                    t.cod_turma as turma_id,
                    esd.ref_cod_disciplina AS disciplina_id,
                    esd.etapas_especificas,
                    esd.etapas_utilizadas,
                    esd.updated_at,
                    null as deleted_at
                FROM pmieducar.turma AS t
                INNER JOIN pmieducar.escola_serie_disciplina AS esd
                    ON TRUE
                    AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                    AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                    AND esd.ativo = 1
                    AND t.ano = ANY(esd.anos_letivos)
                WHERE TRUE
                    {$whereEscolaSerieDisciplina}
                    AND t.ano = $2
                    AND t.ref_ref_cod_escola IN ({$escola})
                    AND esd.etapas_especificas = 1
                    AND NOT EXISTS(
                        SELECT 1
                        FROM
                            modules.componente_curricular_turma AS cct,
                            pmieducar.instituicao AS i
                        WHERE TRUE
                        AND cct.turma_id = t.cod_turma
                        AND i.cod_instituicao = $1
                        AND i.componente_curricular_turma
                    )
            )
            UNION ALL
            (
                SELECT
                    t.cod_turma as turma_id,
                    esd.ref_cod_disciplina AS disciplina_id,
                    esd.etapas_especificas,
                    esd.etapas_utilizadas,
                    esd.updated_at,
                    esd.deleted_at
                FROM pmieducar.turma AS t
                INNER JOIN pmieducar.escola_serie_disciplina_excluidos AS esd
                    ON TRUE
                    AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                    AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                    AND esd.ativo = 1
                    AND t.ano = ANY(esd.anos_letivos)
                WHERE TRUE
                    {$whereEscolaSerieDisciplina}
                    AND t.ano = $2
                    AND t.ref_ref_cod_escola IN ({$escola})
                    AND esd.etapas_especificas = 1
                    AND NOT EXISTS(
                        SELECT 1
                        FROM
                            modules.componente_curricular_turma AS cct,
                            pmieducar.instituicao AS i
                        WHERE TRUE
                        AND cct.turma_id = t.cod_turma
                        AND i.cod_instituicao = $1
                        AND i.componente_curricular_turma
                    )
            )
            UNION ALL
            (
                SELECT
                    cct.turma_id,
                    cct.componente_curricular_id AS disciplina_id,
                    cct.etapas_especificas,
                    cct.etapas_utilizadas,
                    cct.updated_at,
                    null as deleted_at
                FROM modules.componente_curricular_turma AS cct
                INNER JOIN pmieducar.turma t
                ON t.cod_turma = cct.turma_id
                WHERE TRUE
                    {$whereComponenteCurricularTurma}
                    AND t.ano = $2
                    AND t.ref_ref_cod_escola IN ({$escola})
                    AND cct.etapas_especificas = 1
                    AND EXISTS(
                        SELECT 1
                        FROM pmieducar.instituicao AS i
                        WHERE i.cod_instituicao = $1
                        AND i.componente_curricular_turma
                    )
            )
            UNION ALL
            (
                SELECT
                    cct.turma_id,
                    cct.componente_curricular_id AS disciplina_id,
                    cct.etapas_especificas,
                    cct.etapas_utilizadas,
                    cct.updated_at,
                    cct.deleted_at
                FROM modules.componente_curricular_turma_excluidos AS cct
                INNER JOIN pmieducar.turma t
                ON t.cod_turma = cct.turma_id
                WHERE TRUE
                    {$whereComponenteCurricularTurma}
                    AND t.ano = $2
                    AND t.ref_ref_cod_escola IN ({$escola})
                    AND cct.etapas_especificas = 1
                    AND EXISTS(
                        SELECT 1
                        FROM pmieducar.instituicao AS i
                        WHERE i.cod_instituicao = $1
                        AND i.componente_curricular_turma
                    )
            )
            order by updated_at
        ";

        $etapas = $this->fetchPreparedQuery($sql, $params);
        $etapas = Portabilis_Array_Utils::filterSet($etapas, [
            'turma_id', 'disciplina_id', 'etapas_especificas', 'etapas_utilizadas', 'updated_at', 'deleted_at'
        ]);

        return [
            'etapas' => $etapas
        ];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'etapas-especificas')) {
            $this->appendResponse($this->getEtapasEspecificas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
