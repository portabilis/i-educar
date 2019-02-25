<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';

class EtapasController extends ApiCoreController
{
    protected function getEtapasEspecificas()
    {
        if (empty($this->validatesPresenceOf('instituicao_id'))) {
            return [];
        }

        $instituicaoId = $this->getRequest()->instituicao_id;

        $sql = '
            SELECT 
                t.cod_turma as turma_id,
                esd.ref_cod_disciplina AS disciplina_id,
                esd.etapas_utilizadas,
                esd.updated_at
            FROM turma AS t
            INNER JOIN escola_serie_disciplina AS esd
                ON TRUE 
                AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                AND esd.ativo = 1
                AND t.ano = ANY(esd.anos_letivos)
            WHERE TRUE 
                AND esd.etapas_especificas = 1
                AND NOT EXISTS(
                    SELECT 1
                    FROM 
                        componente_curricular_turma AS cct,
                        pmieducar.instituicao AS i
                    WHERE TRUE 
                    AND cct.turma_id = t.cod_turma
                    AND i.cod_instituicao = 1
                    AND i.componente_curricular_turma
                )
            UNION ALL
            SELECT 
                cct.turma_id,
                cct.componente_curricular_id AS disciplina_id,
                cct.etapas_utilizadas,
                cct.updated_at
            FROM modules.componente_curricular_turma AS cct
            WHERE TRUE 
                AND cct.etapas_especificas = 1
                AND EXISTS(
                    SELECT 1
                    FROM pmieducar.instituicao AS i
                    WHERE i.cod_instituicao = $1
                    AND i.componente_curricular_turma
                )
        ';

        $etapas = $this->fetchPreparedQuery($sql, [$instituicaoId]);
        $etapas = Portabilis_Array_Utils::filterSet($etapas, [
            'turma_id', 'disciplina_id', 'etapas_utilizadas', 'updated_at', 'deleted_at'
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
