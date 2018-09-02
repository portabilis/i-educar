<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';

class EtapasController extends ApiCoreController
{

    protected function canGetTurmasComEtapasEspecificas()
    {
        return $this->validatesPresenceOf('instituicao_id');
    }

    protected function getTurmasComEtapasEspecificas()
    {
        if ($this->canGetTurmasComEtapasEspecificas()) {
            $instituicaoId = $this->getRequest()->instituicao_id;

            $sql = 'SELECT t.cod_turma AS turma_id
                      FROM turma AS t
                INNER JOIN escola_serie_disciplina AS esd
                        ON esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                       AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                       AND esd.ativo = 1
                       AND t.ano = ANY(esd.anos_letivos)
                     WHERE t.ativo = 1
                       AND esd.etapas_especificas = 1
                       AND NOT EXISTS(SELECT 1
                                        FROM componente_curricular_turma AS cct,
                                             pmieducar.instituicao AS i
                                       WHERE cct.turma_id = t.cod_turma
                                         AND i.cod_instituicao = 1
                                         AND i.componente_curricular_turma)
                  GROUP BY t.cod_turma
                 UNION ALL
                    SELECT cct.turma_id AS turma_id
                      FROM modules.componente_curricular_turma AS cct
                     WHERE cct.etapas_especificas = 1
                       AND EXISTS(SELECT 1
                                    FROM turma AS t
                                   WHERE t.cod_turma = cct.turma_id
                                     AND t.ativo = 1)
                       AND EXISTS(SELECT 1
                                    FROM pmieducar.instituicao AS i
                                   WHERE i.cod_instituicao = $1
                                     AND i.componente_curricular_turma)
                  GROUP BY cct.turma_id';

            $turmas = $this->fetchPreparedQuery($sql, [$instituicaoId]);
            $turmas = Portabilis_Array_Utils::filterSet($turmas, 'turma_id');

            return ['turmas' => $turmas];
        }
    }

    protected function canGetEtapasEspecificasPorDisciplina()
    {
        return (
            $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('turma_id')
        );
    }

    protected function getEtapasEspecificasPorDisciplina()
    {
        if ($this->canGetEtapasEspecificasPorDisciplina()) {
            $instituicaoId = $this->getRequest()->instituicao_id;
            $turmaId = $this->getRequest()->turma_id;

            $sql = 'SELECT esd.ref_cod_disciplina AS disciplina_id,
                           esd.etapas_utilizadas,
                           esd.updated_at
                      FROM turma AS t
                INNER JOIN escola_serie_disciplina AS esd
                        ON esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                       AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                       AND esd.ativo = 1
                       AND t.ano = ANY(esd.anos_letivos)
                     WHERE t.cod_turma = $1
                       AND esd.etapas_especificas = 1
                       AND NOT EXISTS(SELECT 1
                                        FROM componente_curricular_turma AS cct,
                                             pmieducar.instituicao AS i
                                       WHERE cct.turma_id = t.cod_turma
                                         AND i.cod_instituicao = 1
                                         AND i.componente_curricular_turma)
                 UNION ALL
                    SELECT cct.componente_curricular_id AS disciplina_id,
                           cct.etapas_utilizadas,
                           cct.updated_at
                      FROM modules.componente_curricular_turma AS cct
                     WHERE cct.turma_id = $1
                       AND cct.etapas_especificas = 1
                       AND EXISTS(SELECT 1
                                    FROM pmieducar.instituicao AS i
                                   WHERE i.cod_instituicao = $2
                                     AND i.componente_curricular_turma)';

            $etapas = $this->fetchPreparedQuery($sql, [$turmaId, $instituicaoId]);

            $attrs = ['disciplina_id', 'etapas_utilizadas', 'updated_at'];
            $etapas = Portabilis_Array_Utils::filterSet($etapas, $attrs);

            return ['etapas' => $etapas];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'turmas-com-etapas-especificas')) {
            $this->appendResponse($this->getTurmasComEtapasEspecificas());
        } elseif ($this->isRequestFor('get', 'etapas-especificas-por-disciplina')) {
            $this->appendResponse($this->getEtapasEspecificasPorDisciplina());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
